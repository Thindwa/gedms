@extends('layouts.app')
@section('content')
<div x-data="rolesManager()" class="space-y-5">
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Roles & Permissions</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $roles->count() }} roles &middot; {{ $permissions->count() }} permissions</p>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="showAddRole = true" class="btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Role
            </button>
            <button type="button" @click="showAddPerm = true" class="btn-secondary text-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Add Permission
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    @unless ($isSystemAdmin)
    <div class="flex items-start gap-3 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 text-sm">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div><strong>Department scope:</strong> You manage department-level roles and permissions. System-level roles are managed by the System Administrator.</div>
    </div>
    @endunless

    {{-- Main form --}}
    <form action="{{ route('admin.roles.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Role cards --}}
        <div class="space-y-3">
            @foreach ($roles as $role)
            <div class="card overflow-visible" x-data="{ expanded: false }">
                <input type="hidden" name="roles[{{ $role->id }}][id]" value="{{ $role->id }}">

                {{-- Role header --}}
                <button type="button" @click="expanded = !expanded"
                        class="w-full flex items-center justify-between px-5 py-4 hover:bg-slate-50/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-9 h-9 rounded-lg"
                             :class="expanded ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-500'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold text-slate-800">{{ $role->name }}</div>
                            <div class="text-xs text-slate-500">{{ $role->permissions->count() }} of {{ $permissions->count() }} permissions</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden sm:flex items-center gap-1">
                            @php $enabledCount = $role->permissions->count(); @endphp
                            <div class="w-24 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all {{ $enabledCount > 0 ? 'bg-teal-500' : 'bg-slate-300' }}"
                                     style="width: {{ $permissions->count() > 0 ? round($enabledCount / $permissions->count() * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500 w-8 text-right">{{ $permissions->count() > 0 ? round($enabledCount / $permissions->count() * 100) : 0 }}%</span>
                        </div>
                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>

                {{-- Expanded permission body --}}
                <div x-show="expanded" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-slate-200">
                    {{-- Role actions bar --}}
                    <div class="flex items-center justify-between px-5 py-2.5 bg-slate-50/80 border-b border-slate-100" @click.stop>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <button type="button" class="hover:text-teal-600 transition-colors" @click="toggleAll('{{ $role->id }}', true)" title="Check all">Select all</button>
                            <span>&middot;</span>
                            <button type="button" class="hover:text-red-600 transition-colors" @click="toggleAll('{{ $role->id }}', false)" title="Uncheck all">Clear all</button>
                        </div>
                        <div class="flex items-center gap-2" x-data="{ editing: false }">
                            <template x-if="!editing">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="editing = true" class="text-xs text-slate-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Rename
                                    </button>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Delete role &quot;{{ $role->name }}&quot;? Users assigned this role will lose it.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-slate-500 hover:text-red-600 transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </template>
                            <template x-if="editing">
                                <form action="{{ route('admin.roles.update-role', $role) }}" method="POST" class="flex items-center gap-2" @click.stop>
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $role->name }}" class="input-field text-xs py-1 px-2 w-36" required>
                                    <button type="submit" class="text-xs text-teal-600 font-medium hover:text-teal-700">Save</button>
                                    <button type="button" @click="editing = false" class="text-xs text-slate-400 hover:text-slate-600">Cancel</button>
                                </form>
                            </template>
                        </div>
                    </div>

                    {{-- Permission groups --}}
                    <div class="p-5 space-y-5">
                        @php
                            $grouped = $permissions->groupBy(function ($p) {
                                if (str_starts_with($p->name, 'view-file') || str_starts_with($p->name, 'create-file') || str_starts_with($p->name, 'edit-file') || str_starts_with($p->name, 'delete-file') || str_starts_with($p->name, 'share-file'))
                                    return 'File Management';
                                if (str_starts_with($p->name, 'view-doc') || str_starts_with($p->name, 'create-doc') || str_starts_with($p->name, 'edit-doc') || str_starts_with($p->name, 'approve-doc'))
                                    return 'Document Management';
                                if (str_starts_with($p->name, 'manage-'))
                                    return 'Administration';
                                if (str_starts_with($p->name, 'view-audit'))
                                    return 'Audit & Compliance';
                                return 'Other';
                            })->sortKeys();
                            $groupIcons = [
                                'File Management' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
                                'Document Management' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'Administration' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                                'Audit & Compliance' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                'Other' => 'M13 10V3L4 14h7v7l9-11h-7z',
                            ];
                            $groupColors = [
                                'File Management' => 'text-blue-600 bg-blue-50',
                                'Document Management' => 'text-amber-600 bg-amber-50',
                                'Administration' => 'text-purple-600 bg-purple-50',
                                'Audit & Compliance' => 'text-emerald-600 bg-emerald-50',
                                'Other' => 'text-slate-600 bg-slate-100',
                            ];
                        @endphp

                        @foreach ($grouped as $group => $perms)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex items-center justify-center w-6 h-6 rounded {{ $groupColors[$group] ?? 'text-slate-600 bg-slate-100' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $groupIcons[$group] ?? $groupIcons['Other'] }}"/></svg>
                                </div>
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $group }}</h4>
                                <div class="flex-1 h-px bg-slate-200"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-1.5">
                                @foreach ($perms as $perm)
                                <label class="flex items-center justify-between py-1.5 px-2 rounded-md hover:bg-slate-50 transition-colors cursor-pointer group"
                                       x-data="{ on: {{ $role->hasPermissionTo($perm) ? 'true' : 'false' }} }">
                                    <span class="text-sm text-slate-700 group-hover:text-slate-900">{{ $perm->name }}</span>
                                    <input type="checkbox"
                                           name="roles[{{ $role->id }}][permissions][]"
                                           value="{{ $perm->name }}"
                                           data-role-id="{{ $role->id }}"
                                           x-model="on"
                                           class="hidden">
                                    <button type="button" @click.prevent="on = !on"
                                            class="relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                                            :class="on ? 'bg-teal-500' : 'bg-slate-300'"
                                            role="switch" :aria-checked="on">
                                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                              :class="on ? 'translate-x-4' : 'translate-x-0'"></span>
                                    </button>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Save button --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('admin.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Admin
            </a>
            <button type="submit" class="btn-primary px-6">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </form>

    {{-- Permissions list --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-slate-800 text-sm">All Permissions</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $permissions->count() }} permissions defined</p>
            </div>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-2">
                @foreach ($permissions as $perm)
                <div class="inline-flex items-center gap-1.5 pl-3 pr-1.5 py-1.5 rounded-full bg-slate-100 text-sm text-slate-700 group hover:bg-slate-200/80 transition-colors">
                    <span>{{ $perm->name }}</span>
                    <form action="{{ route('admin.permissions.destroy', $perm) }}" method="POST" class="inline" onsubmit="return confirm('Delete &quot;{{ $perm->name }}&quot;? It will be removed from all roles.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-5 h-5 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-100 hover:text-red-600 transition-colors" title="Delete">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Add Role Modal --}}
    <template x-teleport="body">
        <div x-show="showAddRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showAddRole = false" x-transition.opacity>
            <form action="{{ route('admin.roles.store') }}" method="POST"
                  class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop x-transition.scale.95>
                @csrf
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-teal-100 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">New Role</h3>
                        <p class="text-xs text-slate-500">Create a new role to assign permissions</p>
                    </div>
                </div>
                <input type="text" name="name" required placeholder="e.g. Senior Officer" class="input-field w-full mb-4" autofocus>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="showAddRole = false" class="btn-secondary text-sm">Cancel</button>
                    <button type="submit" class="btn-primary text-sm">Create Role</button>
                </div>
            </form>
        </div>
    </template>

    {{-- Add Permission Modal --}}
    <template x-teleport="body">
        <div x-show="showAddPerm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm" @click.self="showAddPerm = false" x-transition.opacity>
            <form action="{{ route('admin.permissions.store') }}" method="POST"
                  class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4" @click.stop x-transition.scale.95>
                @csrf
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100 text-purple-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800">New Permission</h3>
                        <p class="text-xs text-slate-500">Use kebab-case naming convention</p>
                    </div>
                </div>
                <input type="text" name="name" required placeholder="e.g. manage-reports" class="input-field w-full mb-4" autofocus>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="showAddPerm = false" class="btn-secondary text-sm">Cancel</button>
                    <button type="submit" class="btn-primary text-sm">Create Permission</button>
                </div>
            </form>
        </div>
    </template>
</div>

@push('scripts')
<script>
function rolesManager() {
    return {
        showAddRole: false,
        showAddPerm: false,
        toggleAll(roleId, state) {
            document.querySelectorAll(`input[data-role-id="${roleId}"]`).forEach(cb => {
                cb.checked = state;
                cb.dispatchEvent(new Event('input', { bubbles: true }));
                const label = cb.closest('[x-data]');
                if (label && label.__x) {
                    label.__x.$data.on = state;
                } else if (label && label._x_dataStack) {
                    label._x_dataStack[0].on = state;
                }
            });
        }
    };
}
</script>
@endpush
@endsection
