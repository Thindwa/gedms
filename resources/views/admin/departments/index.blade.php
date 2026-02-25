@extends('layouts.app')

@section('content')
<div class="space-y-4">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-slate-800">Departments</h1>
        @can('manage-ministry')
            <a href="{{ route('admin.departments.create') }}" class="btn-primary text-sm">+ Add Department</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full" data-datatable>
            <thead class="table-header">
                <tr>
                    <th>Name</th>
                    <th>Ministry</th>
                    <th>Code</th>
                    <th>Users</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $d)
                    <tr class="table-row">
                        <td class="font-medium">{{ $d->name }}</td>
                        <td class="text-slate-600">{{ $d->ministry?->name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $d->code ?? '—' }}</td>
                        <td>{{ $d->users_count ?? 0 }}</td>
                        <td><span class="badge {{ $d->is_active ? 'badge-green' : 'badge-gray' }}">{{ $d->is_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                @empty
                    <tr><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No departments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Admin</a>
</div>
@endsection
