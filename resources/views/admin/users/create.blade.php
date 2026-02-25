@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">Create User</h1>

    @if ($ministries->isEmpty() || $departments->isEmpty())
        <div class="card card-body bg-amber-50 border-amber-200 text-amber-800 mb-4">
            <p class="font-medium">Setup required</p>
            <p class="text-sm mt-1">Before creating users, you need ministries and departments. Run the database seeder or create them first:</p>
            <div class="flex gap-3 mt-2 text-sm">
                @can('manage-ministry')
                    <a href="{{ route('admin.ministries.create') }}" class="text-blue-600 hover:underline">+ Add Ministry</a>
                    <a href="{{ route('admin.departments.create') }}" class="text-blue-600 hover:underline">+ Add Department</a>
                @endcan
                <a href="{{ route('admin.index') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="card">
        <div class="card-body">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-input-label for="name">Name</x-input-label>
                    <x-text-input id="name" name="name" value="{{ old('name') }}" class="input-field mt-1" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="email">Email</x-input-label>
                    <x-text-input id="email" name="email" type="email" value="{{ old('email') }}" class="input-field mt-1" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="password">Password</x-input-label>
                    <x-text-input id="password" name="password" type="password" class="input-field mt-1" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="password_confirmation">Confirm Password</x-input-label>
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="input-field mt-1" required autocomplete="new-password" />
                </div>
                <div>
                    <x-input-label for="role">Role</x-input-label>
                    <select id="role" name="role" class="input-field mt-1" required>
                        <option value="">— Select role —</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" @selected(old('role') === $r->name)>{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>
                @if (auth()->user()->can('manage-department') && !auth()->user()->can('manage-system') && auth()->user()->department_id)
                    {{-- Department Admin: Ministry and Department are pre-filled in the background --}}
                    <input type="hidden" name="ministry_id" value="{{ auth()->user()->ministry_id }}">
                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                @else
                    {{-- System Admin: Select Ministry and Department (no Section — creates department admins) --}}
                    <div>
                        <x-input-label for="ministry_id">Ministry</x-input-label>
                        <select id="ministry_id" name="ministry_id" class="input-field mt-1" onchange="filterDepartments(this.value)">
                            <option value="">— None —</option>
                            @foreach ($ministries as $m)
                                <option value="{{ $m->id }}" @selected(old('ministry_id') == $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('ministry_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="department_id">Department</x-input-label>
                        <select id="department_id" name="department_id" class="input-field mt-1">
                            <option value="">— None —</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" data-ministry="{{ $d->ministry_id }}" @selected(old('department_id') == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
                    </div>
                @endif
                @if (auth()->user()->can('manage-department') && !auth()->user()->can('manage-system'))
                    {{-- Department Admin only: Section dropdown (for Officers, Clerks, etc.) --}}
                    <div>
                        <x-input-label for="section_id">Section (optional)</x-input-label>
                        <select id="section_id" name="section_id" class="input-field mt-1">
                            <option value="">— None —</option>
                            @foreach ($sections as $s)
                                <option value="{{ $s->id }}" @selected(old('section_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Assign to a section for Officers, Clerks, etc. Leave empty for Department Admin, Director.</p>
                    </div>
                @endif
                <div>
                    <label class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        <span class="ml-2 text-sm text-slate-600">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit" class="btn-primary">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
function filterDepartments(ministryId) {
    const deptSelect = document.getElementById('department_id');
    if (!deptSelect) return;
    Array.from(deptSelect.options).forEach(opt => {
        if (opt.value === '') opt.style.display = '';
        else opt.style.display = (!ministryId || opt.dataset.ministry == ministryId) ? '' : 'none';
    });
    deptSelect.value = '';
}
</script>
@endsection
