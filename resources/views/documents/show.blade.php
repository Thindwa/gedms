<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <span class="truncate">{{ $document->title }}</span>
            <span class="badge {{ $document->status === 'draft' ? 'badge-gray' : ($document->status === 'under_review' ? 'badge-yellow' : ($document->status === 'approved' ? 'badge-green' : 'badge-gray')) }}">
                {{ str_replace('_', ' ', ucfirst($document->status)) }}
            </span>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (!$document->file)
            <div class="card card-body bg-amber-50 border-amber-200 text-amber-800">The associated file is no longer available. Download and check-in are disabled.</div>
        @endif

        {{-- 3-Panel EDMS layout: Metadata | Workflow | Audit Trail --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Panel 1: Document Metadata --}}
            <div class="card">
                <div class="p-4 border-b border-slate-200 bg-slate-50 font-semibold text-slate-700">Document Metadata</div>
                <dl class="p-4 space-y-3">
                    <div><dt class="text-xs text-slate-500">Title</dt><dd class="font-medium text-slate-800">{{ $document->title }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Document Type</dt><dd class="font-medium text-slate-800">{{ $document->documentType->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Ministry</dt><dd class="font-medium text-slate-800">{{ $document->ministry->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Department</dt><dd class="font-medium text-slate-800">{{ $document->department->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Owner</dt><dd class="font-medium text-slate-800">{{ $document->owner->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Sensitivity</dt><dd class="font-medium text-slate-800">{{ $document->sensitivityLevel->name }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Version</dt><dd class="font-medium text-slate-800">{{ $document->current_version }}</dd></div>
                    @if ($document->legal_hold)
                        <div><span class="badge-red">Legal Hold</span></div>
                    @endif
                    @if ($document->isCheckedOut())
                        <div><dt class="text-xs text-slate-500">Checked out by</dt><dd class="font-medium text-slate-800">{{ $document->checkedOutBy->name }} ({{ $document->checked_out_at->format('M j, Y H:i') }})</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Panel 2: Workflow & Approval --}}
            <div class="card">
                <div class="p-4 border-b border-slate-200 bg-slate-50 font-semibold text-slate-700">Workflow & Approval</div>
                <div class="p-4 space-y-4">
                    @if ($activeWorkflow && $currentSteps->isNotEmpty())
                        @php
                            $allSteps = $activeWorkflow->stepInstances->sortBy('id');
                            $completed = $allSteps->where('status', 'approved');
                            $pending = $allSteps->where('status', 'pending');
                        @endphp
                        <div class="space-y-3">
                            @foreach ($activeWorkflow->stepInstances->sortBy(fn($s) => $s->workflowStep?->step_order ?? 0) as $si)
                                @php
                                    $ws = $si->workflowStep;
                                    $isCurrent = $si->status === 'pending' && app(\App\Services\WorkflowService::class)->canApproveStep($si, auth()->user());
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-lg {{ $si->status === 'approved' ? 'bg-emerald-50' : ($isCurrent ? 'bg-blue-50' : 'bg-slate-50') }}">
                                    {{-- Green circle + check (completed), Blue circle (active), Grey circle (pending) per mockup --}}
                                    @if ($si->status === 'approved')
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        </div>
                                    @elseif ($isCurrent)
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-blue-600 bg-blue-50"></div>
                                    @else
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 border-slate-300 bg-white"></div>
                                    @endif
                                    <div class="flex-1">
                                        <span class="font-medium">{{ $ws->name }}</span>
                                        <span class="text-slate-500 text-sm">({{ $ws->role_name }})</span>
                                    </div>
                                    @if ($si->status === 'pending' && app(\App\Services\WorkflowService::class)->canApproveStep($si, auth()->user()))
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <form action="{{ route('documents.workflow.approve', [$document, $si]) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="text" name="comment" placeholder="Comment" class="input-field w-28 text-sm py-1.5 inline-block mr-1">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Approve</button>
                                            </form>
                                            <form action="{{ route('documents.workflow.reject', [$document, $si]) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="text" name="comment" placeholder="Reason" class="input-field w-28 text-sm py-1.5 inline-block mr-1">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Reject</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif ($document->status === 'under_review' && !$activeWorkflow && auth()->user()->can('approve-documents'))
                        <div class="flex flex-wrap gap-2">
                            <form action="{{ route('documents.approve', $document) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Approve</button>
                            </form>
                            <form action="{{ route('documents.reject', $document) }}" method="POST" class="inline">
                                @csrf
                                <input type="text" name="comment" placeholder="Rejection reason" class="input-field inline-block w-40 text-sm py-1.5 mr-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Reject</button>
                            </form>
                        </div>
                    @else
                        <p class="text-slate-500 text-sm">No active workflow.</p>
                    @endif
                </div>
            </div>

            {{-- Panel 3: Audit Trail --}}
            <div class="card">
                <div class="p-4 border-b border-slate-200 bg-slate-50 font-semibold text-slate-700">Audit Trail</div>
                <div class="p-4 max-h-80 overflow-y-auto space-y-2">
                    @forelse ($auditTrail as $log)
                        @php
                            $label = match ($log->action) {
                                'workflow.step_approved' => 'Approved',
                                'workflow.step_rejected' => 'Rejected',
                                'workflow.started' => 'Workflow started',
                                'document.promoted' => 'Promoted',
                                'document.submitted' => 'Submitted',
                                'document.approved' => 'Approved',
                                'document.rejected' => 'Rejected',
                                'document.archived' => 'Archived',
                                'document.checkout' => 'Checked out',
                                'document.checkin' => 'Checked in',
                                'document.cancel_checkout' => 'Check-out cancelled',
                                'document.download' => 'Downloaded',
                                default => str_replace('.', ' ', $log->action),
                            };
                        @endphp
                        <div class="flex items-start gap-2 text-sm">
                            <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                            <div>
                                <span class="text-slate-500">{{ $log->created_at?->format('m/d') }}</span>
                                <span class="font-medium">{{ $label }}</span>
                                <span class="text-slate-600">by {{ $log->user?->name ?? $log->user_email ?? '—' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm">No audit entries.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Version History --}}
        <div class="card">
            <div class="card-body">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Version History</h3>
                <ul class="space-y-2 divide-y divide-slate-100">
                    @foreach ($document->versions as $v)
                        <li class="flex justify-between items-center py-3 first:pt-0">
                            <span class="text-slate-700">Version {{ $v->version }}
                                @if ($v->isApproved()) <span class="text-emerald-600 text-sm font-medium">(Approved)</span> @endif
                                — {{ $v->creator?->name ?? '—' }} · {{ $v->created_at->format('M j, Y') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2">
            @if ($document->file)
                <a href="{{ route('documents.download', $document) }}" class="btn-primary">Download</a>
            @endif
            @if ($document->status === 'draft' && $document->canEdit(auth()->user()))
                @if (!$document->requires_workflow)
                    <form action="{{ route('documents.promote-to-approved', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-primary !bg-emerald-600">Promote to Approved</button></form>
                @elseif ($document->isCheckedOut() && $document->checked_out_by === auth()->id() && $document->file)
                    <form action="{{ route('documents.checkin', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-primary !bg-blue-600">Check In</button></form>
                    <form action="{{ route('documents.cancel-checkout', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-secondary">Cancel Check-out</button></form>
                @elseif (!$document->isCheckedOut())
                    <form action="{{ route('documents.checkout', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-primary !bg-blue-600">Check Out</button></form>
                    <form action="{{ route('documents.submit', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-primary !bg-amber-600">Submit for Review</button></form>
                @endif
            @endif
            @if (auth()->user()->can('manage-retention-disposition'))
                <form action="{{ route('documents.legal-hold', $document) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary {{ $document->legal_hold ? '' : '!border-red-300 !text-red-700' }}">{{ $document->legal_hold ? 'Remove Legal Hold' : 'Apply Legal Hold' }}</button>
                </form>
            @endif
            @if ($document->status === 'approved' && auth()->user()->can('approve-documents'))
                <form action="{{ route('documents.archive', $document) }}" method="POST" class="inline">@csrf<button type="submit" class="btn-secondary">Archive</button></form>
            @endif
            @if ($document->file)
                <a href="{{ route('files.index', ['space' => $document->file->storage_space_id, 'folder' => $document->file->folder_id]) }}" class="btn-secondary">View in File Manager</a>
            @endif
        </div>
    </div>
</x-app-layout>
