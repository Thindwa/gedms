<?php

namespace App\Services;

use App\Models\Document;
use App\Models\File;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Unified search across files and documents.
 * Supports PostgreSQL FTS (tsvector) and MySQL FULLTEXT. Falls back to LIKE for short queries on MySQL.
 * Interface designed for future ElasticSearch adapter.
 */
class SearchService
{
    public function search(
        string $query,
        ?int $ministryId = null,
        ?int $departmentId = null,
        ?int $documentTypeId = null,
        ?string $status = null,
        ?int $sensitivityLevelId = null,
        ?int $year = null,
        ?string $type = null, // 'file', 'document', or null for both
        int $perPage = 20
    ): LengthAwarePaginator {
        $tsQuery = trim($query);
        if (strlen($tsQuery) < 2) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1);
        }

        $results = collect();

        if (!$type || $type === 'file') {
            $files = $this->searchFiles($tsQuery, $ministryId, $departmentId, $perPage * 2);
            foreach ($files as $f) {
                $results->push([
                    'type' => 'file',
                    'id' => $f->id,
                    'uuid' => $f->uuid,
                    'title' => $f->name,
                    'space' => $f->storageSpace?->name,
                    'updated_at' => $f->updated_at,
                ]);
            }
        }

        if (!$type || $type === 'document') {
            $docs = $this->searchDocuments($tsQuery, $ministryId, $departmentId, $documentTypeId, $status, $sensitivityLevelId, $year, $perPage);
            foreach ($docs as $d) {
                $results->push([
                    'type' => 'document',
                    'id' => $d->id,
                    'uuid' => $d->uuid,
                    'title' => $d->title,
                    'status' => $d->status,
                    'document_type' => $d->documentType?->name,
                    'updated_at' => $d->updated_at,
                ]);
            }
        }

        $total = $results->count();
        $page = request()->get('page', 1);
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $results->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    protected function searchFiles(string $tsQuery, ?int $ministryId, ?int $departmentId, int $limit)
    {
        $q = File::query()->with('storageSpace');

        $this->applySearchWhere($q, 'files', 'name', $tsQuery);

        if ($ministryId || $departmentId) {
            $q->whereHas('storageSpace', function ($sq) use ($ministryId, $departmentId) {
                if ($ministryId) {
                    $sq->where('owner_ministry_id', $ministryId)
                        ->orWhereHas('ownerDepartment', fn ($d) => $d->where('ministry_id', $ministryId))
                        ->orWhereHas('ownerUser', fn ($u) => $u->where('ministry_id', $ministryId));
                }
                if ($departmentId) {
                    $sq->where('owner_department_id', $departmentId);
                }
            });
        }

        $this->applySearchOrder($q, 'files', 'name', $tsQuery);

        return $q->limit($limit)->get();
    }

    protected function searchDocuments(
        string $tsQuery,
        ?int $ministryId,
        ?int $departmentId,
        ?int $documentTypeId,
        ?string $status,
        ?int $sensitivityLevelId,
        ?int $year,
        int $limit
    ) {
        $q = Document::query()->with('documentType');

        $this->applySearchWhere($q, 'documents', 'title', $tsQuery);

        if ($ministryId) {
            $q->where('ministry_id', $ministryId);
        }
        if ($departmentId) {
            $q->where('department_id', $departmentId);
        }
        if ($documentTypeId) {
            $q->where('document_type_id', $documentTypeId);
        }
        if ($status) {
            $q->where('status', $status);
        }
        if ($sensitivityLevelId) {
            $q->where('sensitivity_level_id', $sensitivityLevelId);
        }
        if ($year) {
            $q->whereYear('created_at', $year);
        }

        $this->applySearchOrder($q, 'documents', 'title', $tsQuery);

        return $q->limit($limit)->get();
    }

    protected function applySearchWhere(Builder $query, string $table, string $column, string $tsQuery): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $query->whereRaw('search_vector @@ plainto_tsquery(?)', [$tsQuery]);
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // MySQL FULLTEXT min token length is 3 (InnoDB) or 4 (MyISAM); use LIKE for short queries
            if (strlen($tsQuery) >= 3) {
                $query->whereRaw("MATCH({$column}) AGAINST(? IN NATURAL LANGUAGE MODE)", [$tsQuery]);
            } else {
                $like = '%' . addcslashes($tsQuery, '%_\\') . '%';
                $query->where($column, 'LIKE', $like);
            }
        } else {
            $like = '%' . addcslashes($tsQuery, '%_\\') . '%';
            $query->where($column, 'LIKE', $like);
        }
    }

    protected function applySearchOrder(Builder $query, string $table, string $column, string $tsQuery): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $query->orderByRaw('ts_rank(search_vector, plainto_tsquery(?)) DESC', [$tsQuery]);
        } elseif (in_array($driver, ['mysql', 'mariadb']) && strlen($tsQuery) >= 3) {
            $query->orderByRaw("MATCH({$column}) AGAINST(? IN NATURAL LANGUAGE MODE) DESC", [$tsQuery]);
        }
    }

}
