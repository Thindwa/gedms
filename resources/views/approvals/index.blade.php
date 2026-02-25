<x-app-layout>
    <x-slot name="header">Approval Dashboard</x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        <p class="text-sm text-slate-500">Documents pending your approval in a workflow step.</p>

        <div class="card overflow-hidden">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Document</th>
                        <th>Type</th>
                        <th>Owner</th>
                        <th>Step</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingSteps as $step)
                        @php
                            $doc = $step->workflowInstance->document;
                            $workflowStep = $step->workflowStep;
                        @endphp
                        <tr class="table-row">
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="font-medium text-slate-800 hover:text-slate-600">{{ $doc->title }}</a>
                            </td>
                            <td>{{ $doc->documentType?->name ?? '—' }}</td>
                            <td>{{ $doc->owner?->name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $workflowStep?->name ?? '—' }} ({{ $workflowStep?->role_name ?? '—' }})</td>
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="btn-primary text-sm py-1.5">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No pending approvals.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
