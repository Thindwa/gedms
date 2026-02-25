@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="card card-body bg-amber-50 border-amber-200 text-amber-800">{{ session('error') }}</div>
        @endif

        <div>
            <h1 class="text-2xl font-semibold text-slate-800">System Admin Dashboard</h1>
            <p class="text-slate-500 mt-0.5">{{ auth()->user()->ministry?->name ?? 'EDMS' }} / {{ auth()->user()->department?->name ?? 'Admin' }} — Admin Dashboard</p>
        </div>

        {{-- Overview cards (mockup) --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.ministries.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-blue-600">{{ $ministriesCount ?? 0 }}</span>
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-600 mt-1">Ministries</p>
            </a>
            <a href="{{ route('admin.departments.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-emerald-600">{{ $departmentsCount ?? 0 }}</span>
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-600 mt-1">Departments</p>
            </a>
            <a href="{{ route('admin.users.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-sky-600">{{ $activeUsersCount ?? 0 }}</span>
                </div>
                <p class="text-sm font-medium text-slate-600 mt-1">Active Users</p>
            </a>
            <a href="{{ route('documents.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold text-cyan-600">{{ $activeDocumentsCount ?? 0 }}</span>
                </div>
                <p class="text-sm font-medium text-slate-600 mt-1">Active Documents</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Admin Activity --}}
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">Recent Admin Activity</h2>
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-blue-600 hover:underline">View Full Log →</a>
                </div>
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                    @forelse ($recentActivity ?? [] as $log)
                        <div class="p-4 text-sm">
                            <span class="font-medium text-slate-800">{{ $log->user?->name ?? $log->user_email ?? 'System' }}</span>
                            <span class="text-slate-600">— {{ str_replace('.', ' ', $log->action) }}</span>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $log->created_at?->format('m/d/Y g:i A') }}</div>
                        </div>
                    @empty
                        <div class="p-6 text-slate-500 text-sm">No recent activity.</div>
                    @endforelse
                </div>
            </div>

            {{-- System Settings (Document Types) --}}
            <div class="card overflow-hidden">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">System Settings</h2>
                    <a href="{{ route('admin.document-types.create') }}" class="btn-primary text-sm py-1.5">+ Add Type</a>
                </div>
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                    @forelse ($documentTypes ?? [] as $dt)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <span class="font-medium text-slate-800">{{ $dt->name }}</span>
                                <span class="text-slate-500 text-sm ml-2">{{ $dt->ministry?->name ?? 'Internal' }}</span>
                            </div>
                            <a href="{{ route('admin.document-types.edit', $dt) }}" class="text-blue-600 hover:underline text-sm">Edit →</a>
                        </div>
                    @empty
                        <div class="p-6 text-slate-500 text-sm">No document types. <a href="{{ route('admin.document-types.create') }}" class="text-blue-600">Add one</a>.</div>
                    @endforelse
                </div>
            </div>

            {{-- User Administration --}}
            <div class="card overflow-hidden lg:col-span-2">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">User Administration</h2>
                    <a href="{{ route('admin.users.index') }}" class="btn-primary text-sm py-1.5">+ Create User</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full" data-datatable>
                        <thead class="table-header">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Ministry / Department</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users ?? [] as $u)
                                <tr class="table-row">
                                    <td class="font-medium">{{ $u->name }}</td>
                                    <td>{{ $u->getRoleNames()->first() ?? '—' }}</td>
                                    <td class="text-slate-600">{{ $u->ministry?->name ?? '—' }} / {{ $u->department?->name ?? '—' }}</td>
                                    <td><span class="badge {{ $u->is_active ? 'badge-green' : 'badge-gray' }}">{{ $u->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td><a href="{{ route('admin.users.index') }}?user={{ $u->id }}" class="text-blue-600 hover:underline text-sm">Edit</a></td>
                                </tr>
                            @empty
                                <tr><td></td><td></td><td></td><td></td><td class="p-6 text-slate-500 text-sm">No users.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:underline">View all users →</a>
                </div>
            </div>

            {{-- Retention Rules --}}
            <div class="card overflow-hidden lg:col-span-2">
                <div class="p-4 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-800">Retention Rules</h2>
                    <a href="{{ route('admin.retention-rules.create') }}" class="btn-primary text-sm py-1.5">+ Add Rule</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full" data-datatable>
                        <thead class="table-header">
                            <tr>
                                <th>Document Type</th>
                                <th>Retention</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($retentionRules ?? [] as $r)
                                <tr class="table-row">
                                    <td class="font-medium">{{ $r->documentType?->name ?? '—' }}</td>
                                    <td>{{ $r->retention_years }} years</td>
                                    <td>{{ ucfirst($r->action) }}</td>
                                    <td><a href="{{ route('admin.retention-rules.edit', $r) }}" class="text-blue-600 hover:underline text-sm">Edit →</a></td>
                                </tr>
                            @empty
                                <tr><td></td><td></td><td></td><td class="p-6 text-slate-500 text-sm">No retention rules.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-t border-slate-200 bg-slate-50">
                    <a href="{{ route('admin.retention-rules.index') }}" class="text-sm text-blue-600 hover:underline">View all rules →</a>
                </div>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="card p-4 hover:shadow-card-hover transition-all flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <span class="font-medium text-slate-800">{{ $link['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endsection
