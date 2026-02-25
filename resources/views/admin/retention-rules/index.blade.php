@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center gap-4 mb-4">
    <h1 class="text-xl font-semibold text-slate-800">Retention Rules</h1>
    <a href="{{ route('admin.retention-rules.create') }}" class="btn-primary text-sm">+ Add Rule</a>
</div>

<div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="card overflow-hidden">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Document Type</th>
                        <th>Retention (years)</th>
                        <th>Action</th>
                        <th>Disposal requires approval</th>
                        <th>Active</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rules as $r)
                        <tr class="table-row">
                            <td class="font-medium">{{ $r->documentType?->name ?? '—' }}</td>
                            <td>{{ $r->retention_years }}</td>
                            <td>{{ ucfirst($r->action) }}</td>
                            <td>{{ $r->disposal_requires_approval ? 'Yes' : 'No' }}</td>
                            <td>{{ $r->is_active ? 'Yes' : 'No' }}</td>
                            <td class="space-x-2">
                                <a href="{{ route('admin.retention-rules.edit', $r) }}" class="text-slate-600 hover:text-slate-800 font-medium">Edit</a>
                                <form action="{{ route('admin.retention-rules.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete this retention rule?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td></td><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No retention rules.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to Admin</a>
</div>
@endsection
