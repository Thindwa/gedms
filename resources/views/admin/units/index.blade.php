@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-semibold text-slate-800">Units</h1>

    <div class="card overflow-hidden">
        <table class="min-w-full" data-datatable>
            <thead class="table-header">
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Ministry</th>
                    <th>Code</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($units as $u)
                    <tr class="table-row">
                        <td class="font-medium">{{ $u->name }}</td>
                        <td class="text-slate-600">{{ $u->department?->name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $u->department?->ministry?->name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $u->code ?? '—' }}</td>
                        <td><span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                @empty
                    <tr><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No units.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Admin</a>
</div>
@endsection
