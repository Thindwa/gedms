@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-slate-800">Roles & Permissions</h1>
        <div class="flex gap-2">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="inline" x-data="{ open: false }">
                @csrf
                <button type="button" @click="open = true" class="btn-secondary text-sm">+ Add Role</button>
                <template x-teleport="body">
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm" @click.stop>
                            <h3 class="font-semibold text-slate-800 mb-3">Add Role</h3>
                            <input type="text" name="name" required placeholder="Role name" class="input-field w-full mb-4">
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </template>
            </form>
            <form action="{{ route('admin.permissions.store') }}" method="POST" class="inline" x-data="{ open: false }">
                @csrf
                <button type="button" @click="open = true" class="btn-secondary text-sm">+ Add Permission</button>
                <template x-teleport="body">
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="open = false">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm" @click.stop>
                            <h3 class="font-semibold text-slate-800 mb-3">Add Permission</h3>
                            <input type="text" name="name" required placeholder="e.g. manage-users" class="input-field w-full mb-4">
                            <div class="flex gap-2 justify-end">
                                <button type="button" @click="open = false" class="btn-secondary">Cancel</button>
                                <button type="submit" class="btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </template>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="card card-body bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="card card-body bg-amber-50 border-amber-200 text-amber-800">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/50">
                <p class="text-sm text-slate-600">Assign permissions to each role. Changes take effect immediately.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-header">
                        <tr>
                            <th class="sticky left-0 z-10 bg-slate-50">Permission</th>
                            @foreach ($roles as $role)
                                <th class="whitespace-nowrap px-2">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $role->name }}</span>
                                        <div class="flex gap-1" x-data="{ edit: false }">
                                            <form action="{{ route('admin.roles.update-role', $role) }}" method="POST" class="inline" x-show="edit" x-cloak @click.stop>
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $role->name }}" class="input-field text-sm py-1 px-2 w-32" required>
                                                <button type="submit" class="text-blue-600 text-xs">Save</button>
                                            </form>
                                            <button type="button" @click="edit = true" x-show="!edit" class="text-slate-400 hover:text-slate-600 text-xs" title="Edit role">✎</button>
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Delete role &quot;{{ $role->name }}&quot;? Users will lose this role.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs" title="Delete role">×</button>
                                            </form>
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $perm)
                            <tr class="table-row">
                                <td class="sticky left-0 z-10 bg-white font-medium">
                                    <div class="flex items-center gap-2">
                                        <span>{{ $perm->name }}</span>
                                        <form action="{{ route('admin.permissions.destroy', $perm) }}" method="POST" class="inline" onsubmit="return confirm('Delete permission &quot;{{ $perm->name }}&quot;? It will be removed from all roles.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs" title="Delete permission">×</button>
                                        </form>
                                    </div>
                                </td>
                                @foreach ($roles as $role)
                                    <td>
                                        <input type="checkbox"
                                               name="roles[{{ $role->id }}][permissions][]"
                                               value="{{ $perm->name }}"
                                               {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}
                                               class="rounded border-slate-300 text-slate-600 focus:ring-slate-500">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-5 border-t border-slate-200 bg-slate-50/50">
                @foreach ($roles as $role)
                    <input type="hidden" name="roles[{{ $role->id }}][id]" value="{{ $role->id }}">
                @endforeach
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </div>
    </form>

    <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to Admin</a>
</div>
@endsection
