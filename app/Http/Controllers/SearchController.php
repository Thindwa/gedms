<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\Ministry;
use App\Models\SensitivityLevel;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {}

    public function index(Request $request): View
    {
        $query = $request->get('q', '');
        $results = collect();

        if (strlen(trim($query)) >= 2) {
            $results = $this->searchService->search(
                query: $query,
                ministryId: $request->get('ministry_id') ?: Auth::user()?->ministry_id,
                departmentId: $request->get('department_id'),
                documentTypeId: $request->get('document_type_id'),
                status: $request->get('status'),
                sensitivityLevelId: $request->get('sensitivity_level_id'),
                year: $request->get('year') ? (int) $request->get('year') : null,
                type: $request->get('type'),
                perPage: 20
            );
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
            'ministries' => Ministry::where('is_active', true)->get(),
            'documentTypes' => DocumentType::where('is_active', true)->get(),
            'sensitivityLevels' => SensitivityLevel::orderBy('sort_order')->get(),
        ]);
    }
}
