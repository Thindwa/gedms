@extends('layouts.app')

@section('content')
<div class="space-y-4">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-slate-800">Ministries</h1>
        <a href="{{ route('admin.ministries.create') }}" class="btn-primary text-sm">+ Add Ministry</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full" data-datatable>
            <thead class="table-header">
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Departments</th>
                    <th>Users</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ministries as $m)
                    <tr class="table-row">
                        <td class="font-medium">{{ $m->name }}</td>
                        <td class="text-slate-500">{{ $m->code ?? '—' }}</td>
                        <td>{{ $m->departments_count ?? 0 }}</td>
                        <td>{{ $m->users_count ?? 0 }}</td>
                        <td><span class="badge {{ $m->is_active ? 'badge-green' : 'badge-gray' }}">{{ $m->is_active ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                @empty
                    <tr><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No ministries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Admin</a>
</div>
@endsection
