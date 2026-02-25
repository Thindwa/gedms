<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\WorkflowDefinition;
use App\Models\WorkflowStep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkflowDefinitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-workflows');
    }

    public function index(): View
    {
        $user = auth()->user();
        $query = WorkflowDefinition::with(['documentType.ministry', 'steps'])->orderBy('name');

        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $query->where(function ($q) use ($ministryId) {
                $q->whereNull('document_type_id')
                    ->orWhereHas('documentType', fn ($q2) => $q2->where(function ($q3) use ($ministryId) {
                        $q3->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
                    }));
            });
        }

        $workflows = $query->get();
        return view('admin.workflows.index', compact('workflows'));
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
        $documentTypes = $documentTypesQuery->get();

        return view('admin.workflows.form', [
            'workflow' => new WorkflowDefinition(),
            'documentTypes' => $documentTypes,
            'steps' => collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type_id' => 'nullable|exists:document_types,id',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $workflow = WorkflowDefinition::create($validated);

        $this->syncSteps($workflow, $request->input('steps', []));
        $this->syncDocumentTypeLink($workflow, $validated['document_type_id'] ?? null);

        return redirect()->route('admin.workflows.index')->with('success', 'Workflow created.');
    }

    public function edit(WorkflowDefinition $workflow): View
    {
        $user = auth()->user();
        $documentTypesQuery = DocumentType::where('is_active', true)->orderBy('name');
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system')) {
            $documentTypesQuery->where(function ($q) use ($ministryId) {
                $q->whereNull('ministry_id')->orWhere('ministry_id', $ministryId);
            });
            if ($workflow->documentType && $workflow->documentType->ministry_id && $workflow->documentType->ministry_id !== $ministryId) {
                abort(403, 'You can only edit workflows for document types in your ministry.');
            }
        }
        $documentTypes = $documentTypesQuery->get();
        $workflow->load('steps');

        return view('admin.workflows.form', [
            'workflow' => $workflow,
            'documentTypes' => $documentTypes,
            'steps' => $workflow->steps,
        ]);
    }

    public function update(Request $request, WorkflowDefinition $workflow): RedirectResponse
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $workflow->documentType && $workflow->documentType->ministry_id && $workflow->documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only update workflows for document types in your ministry.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'document_type_id' => 'nullable|exists:document_types,id',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $workflow->update($validated);

        $this->syncSteps($workflow, $request->input('steps', []));
        $this->syncDocumentTypeLink($workflow, $validated['document_type_id'] ?? null);

        return redirect()->route('admin.workflows.index')->with('success', 'Workflow updated.');
    }

    public function destroy(WorkflowDefinition $workflow): RedirectResponse
    {
        $user = auth()->user();
        $ministryId = $user->ministry_id ?? $user->department?->ministry_id;
        if ($ministryId && !$user->can('manage-system') && $workflow->documentType && $workflow->documentType->ministry_id && $workflow->documentType->ministry_id !== $ministryId) {
            abort(403, 'You can only delete workflows for document types in your ministry.');
        }
        if ($workflow->instances()->where('status', 'in_progress')->exists()) {
            return back()->with('error', 'Cannot delete: workflow has active instances.');
        }
        DocumentType::where('workflow_definition_id', $workflow->id)->update(['workflow_definition_id' => null]);
        $workflow->steps()->delete();
        $workflow->delete();
        return redirect()->route('admin.workflows.index')->with('success', 'Workflow deleted.');
    }

    private function syncSteps(WorkflowDefinition $workflow, array $stepsData): void
    {
        $keptIds = [];
        $order = 0;
        foreach ($stepsData as $data) {
            $name = $data['name'] ?? '';
            $roleName = $data['role_name'] ?? '';
            if (empty($name) || empty($roleName)) {
                continue;
            }
            $order++;
            $id = $data['id'] ?? null;
            $step = $id && $workflow->steps()->where('id', $id)->exists()
                ? WorkflowStep::where('workflow_definition_id', $workflow->id)->findOrFail($id)
                : new WorkflowStep(['workflow_definition_id' => $workflow->id]);
            $step->step_order = $order;
            $step->name = $name;
            $step->role_name = $roleName;
            $step->is_parallel = !empty($data['is_parallel']);
            $step->save();
            $keptIds[] = $step->id;
        }
        $workflow->steps()->whereNotIn('id', $keptIds)->delete();
    }

    private function syncDocumentTypeLink(WorkflowDefinition $workflow, ?int $documentTypeId): void
    {
        DocumentType::where('workflow_definition_id', $workflow->id)->update(['workflow_definition_id' => null]);
        if ($documentTypeId) {
            DocumentType::where('id', $documentTypeId)->update(['workflow_definition_id' => $workflow->id]);
        }
    }
}
