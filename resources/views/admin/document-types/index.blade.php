@extends('layouts.app')
@section('content')
    <div class="flex justify-between items-center gap-4 mb-4">
        <h1 class="text-xl font-semibold text-slate-800">Document Types</h1>
        <a href="{{ route('admin.document-types.create') }}" class="btn-primary text-sm">+ Add Document Type</a>
    </div>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="card card-body bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="card overflow-hidden">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Ministry</th>
                        <th>Workflow</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documentTypes as $dt)
                        <tr class="table-row">
                            <td class="font-medium">{{ $dt->name }}</td>
                            <td class="text-slate-500">{{ $dt->code }}</td>
                            <td>{{ $dt->ministry?->name ?? '—' }}</td>
                            <td>{{ $dt->workflowDefinition?->name ?? '—' }}</td>
                            <td>{{ $dt->is_active ? 'Yes' : 'No' }}</td>
                            <td class="space-x-2">
                                <a href="{{ route('admin.document-types.edit', $dt) }}" class="text-slate-600 hover:text-slate-800 font-medium">Edit</a>
                                <form action="{{ route('admin.document-types.destroy', $dt) }}" method="POST" class="inline" onsubmit="return confirm('Delete this document type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td></td><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No document types.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to Admin</a>
    </div>
@endsection
