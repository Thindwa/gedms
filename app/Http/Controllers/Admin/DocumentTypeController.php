<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Ministry;
use App\Models\WorkflowDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-document-types');
    }

    public function index(): View
    {
        $user = auth()->user();
        $query = DocumentType::with(['ministry', 'workflowDefinition']);

        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $query->where(function ($q) use ($ministryId) {
                $q->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
            });
        }

        $documentTypes = $query->orderBy('name')->get();

        return view('admin.document-types.index', compact('documentTypes'));
    }

    public function create(): View
    {
        $user = auth()->user();
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        $workflows = WorkflowDefinition::where('is_active', true)->orderBy('name')->get();

        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $ministries = $ministries->where('id', $ministryId);
        }

        return view('admin.document-types.form', [
            'documentType' => new DocumentType(),
            'ministries' => $ministries->values(),
            'workflows' => $workflows,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:document_types,code',
            'description' => 'nullable|string|max:1000',
            'ministry_id' => 'nullable|exists:ministries,id',
            'workflow_definition_id' => 'nullable|exists:workflow_definitions,id',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $validated['ministry_id'] = $ministryId;
        }

        DocumentType::create($validated);

        return redirect()->route('admin.document-types.index')->with('success', 'Document type created.');
    }

    public function edit(DocumentType $documentType): View
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $documentType->ministry_id && $documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only edit document types in your ministry.');
        }
        $ministries = Ministry::where('is_active', true)->orderBy('name')->get();
        $workflows = WorkflowDefinition::where('is_active', true)->orderBy('name')->get();

        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $ministries = $ministries->where('id', $ministryId)->values();
        }

        return view('admin.document-types.form', [
            'documentType' => $documentType,
            'ministries' => $ministries,
            'workflows' => $workflows,
        ]);
    }

    public function update(Request $request, DocumentType $documentType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:document_types,code,' . $documentType->id,
            'description' => 'nullable|string|max:1000',
            'ministry_id' => 'nullable|exists:ministries,id',
            'workflow_definition_id' => 'nullable|exists:workflow_definitions,id',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $validated['ministry_id'] = $ministryId;
        }

        $documentType->update($validated);

        return redirect()->route('admin.document-types.index')->with('success', 'Document type updated.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $documentType->ministry_id && $documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only delete document types in your ministry.');
        }
        if ($documentType->documents()->exists()) {
            return back()->with('error', 'Cannot delete: document type has documents.');
        }
        $documentType->delete();
        return redirect()->route('admin.document-types.index')->with('success', 'Document type deleted.');
    }
}
