<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\RetentionRule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRetention extends Command
{
    protected $signature = 'edms:process-retention';

    protected $description = 'Archive or flag documents past retention period (excludes legal hold)';

    public function handle(): int
    {
        $rules = RetentionRule::where('is_active', true)->with('documentType')->get();
        $archived = 0;

        foreach ($rules as $rule) {
            $cutoff = Carbon::now()->subYears($rule->retention_years);

            $docs = Document::where('document_type_id', $rule->document_type_id)
                ->where('status', Document::STATUS_APPROVED)
                ->where('legal_hold', false)
                ->where('created_at', '<=', $cutoff)
                ->get();

            foreach ($docs as $doc) {
                $doc->update(['status' => Document::STATUS_ARCHIVED]);
                $archived++;
                $this->info("Archived: {$doc->title} (id: {$doc->id})");
            }
        }

        $this->info("Archived {$archived} document(s).");
        return 0;
    }
}
