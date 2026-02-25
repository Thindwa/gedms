<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\RetentionRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RetentionRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-retention');
    }

    public function index(): View
    {
        $user = auth()->user();
        $query = RetentionRule::with('documentType')->orderBy('document_type_id');

        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $query->whereHas('documentType', fn ($q) => $q->where(function ($q2) use ($ministryId) {
                $q2->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
            }));
        }

        $rules = $query->get();
        return view('admin.retention-rules.index', compact('rules'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $documentTypesQuery = DocumentType::where('is_active', true)->orderBy('name');
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $documentTypesQuery->where(function ($q) use ($ministryId) {
                $q->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
            });
        }
        return view('admin.retention-rules.form', [
            'rule' => new RetentionRule(),
            'documentTypes' => $documentTypesQuery->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'retention_years' => 'required|integer|min:0|max:100',
            'action' => 'required|in:archive,dispose',
            'disposal_requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $validated['disposal_requires_approval'] = $request->boolean('disposal_requires_approval');
        $validated['is_active'] = $request->boolean('is_active');

        RetentionRule::updateOrCreate(
            ['document_type_id' => $validated['document_type_id']],
            $validated
        );

        return redirect()->route('admin.retention-rules.index')->with('success', 'Retention rule saved.');
    }

    public function edit(RetentionRule $retentionRule): View
    {
        $user = auth()->user();
        $documentTypesQuery = DocumentType::where('is_active', true)->orderBy('name');
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $documentTypesQuery->where(function ($q) use ($ministryId) {
                $q->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
            });
            if ($retentionRule->documentType && $retentionRule->documentType->ministry_id && $retentionRule->documentType->ministry_id !== $ministryId) {
                abort(403, 'You can only edit retention rules for document types in your ministry.');
            }
        }
        return view('admin.retention-rules.form', [
            'rule' => $retentionRule,
            'documentTypes' => $documentTypesQuery->get(),
        ]);
    }

    public function update(Request $request, RetentionRule $retentionRule): RedirectResponse
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $retentionRule->documentType && $retentionRule->documentType->ministry_id && $retentionRule->documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only update retention rules for document types in your ministry.');
        }

        $validated = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'retention_years' => 'required|integer|min:0|max:100',
            'action' => 'required|in:archive,dispose',
            'disposal_requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);
        $validated['disposal_requires_approval'] = $request->boolean('disposal_requires_approval');
        $validated['is_active'] = $request->boolean('is_active');

        $retentionRule->update($validated);

        return redirect()->route('admin.retention-rules.index')->with('success', 'Retention rule updated.');
    }

    public function destroy(RetentionRule $retentionRule): RedirectResponse
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $retentionRule->documentType && $retentionRule->documentType->ministry_id && $retentionRule->documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only delete retention rules for document types in your ministry.');
        }
        $retentionRule->delete();
        return redirect()->route('admin.retention-rules.index')->with('success', 'Retention rule deleted.');
    }
}
