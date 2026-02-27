@extends('layouts.app')

@section('content')
<div class="space-y-4">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-slate-800">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn-primary text-sm">+ Add User</a>
    </div>

    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-3">
        @if (Auth::user()?->can('manage-system'))
        <select name="ministry_id" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
            <option value="">All ministries</option>
            @foreach ($ministries as $m)
                <option value="{{ $m->id }}" @selected(request('ministry_id') == $m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        @else
        <span class="text-sm text-slate-600 py-2">{{ Auth::user()?->department?->name ?? 'Your department' }}</span>
        @endif
        <select name="role" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
            <option value="">All roles</option>
            @foreach ($roles as $r)
                <option value="{{ $r->name }}" @selected(request('role') === $r->name)>{{ $r->name }}</option>
            @endforeach
        </select>
        <select name="status" class="input-field w-auto min-w-[120px]" onchange="this.form.submit()">
            <option value="">All status</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
    </form>

    <div class="card overflow-hidden">
        <table class="min-w-full" data-datatable>
            <thead class="table-header">
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Ministry / Department</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr class="table-row">
                        <td class="font-medium">{{ $u->name }}</td>
                        <td class="text-slate-600">{{ $u->email }}</td>
                        <td>{{ $u->getRoleNames()->first() ?? $u->role ?? '—' }}</td>
                        <td class="text-slate-600">{{ $u->ministry?->name ?? '—' }} / {{ $u->department?->name ?? '—' }}</td>
                        <td><span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-slate-500 text-sm">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : '—' }}</td>
                        <td><a href="{{ route('admin.users.edit', $u) }}" class="text-blue-600 hover:underline text-sm">Edit</a></td>
                    </tr>
                @empty
                    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ (Auth::user()?->can('manage-department') && !Auth::user()?->can('manage-system')) ? route('admin.department.index') : route('admin.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Admin</a>
</div>
@endsection
