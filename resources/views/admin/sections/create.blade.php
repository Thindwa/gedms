@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">Add Section</h1>

    @if ($departments->isEmpty())
        <div class="card card-body bg-amber-50 border-amber-200 text-amber-800 mb-4">
            <p class="font-medium">No departments found</p>
            <p class="text-sm mt-1">Sections belong to departments. Run the database seeder or create a department first:</p>
            <div class="flex gap-3 mt-2 text-sm">
                @can('manage-ministry')
                    <a href="{{ route('admin.departments.create') }}" class="text-blue-600 hover:underline">+ Add Department</a>
                @endcan
                <a href="{{ route('admin.index') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.sections.store') }}" method="POST" class="card">
        <div class="card-body">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Section Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="input-field" placeholder="e.g. Policy and Planning">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                    <select id="department_id" name="department_id" required class="input-field">
                        <option value="">— Select department —</option>
                        @foreach ($departments as $d)
                            <option value="{{ $d->id }}" @selected(old('department_id') == $d->id)>
                                {{ $d->name }}{{ $d->ministry ? ' (' . $d->ministry->name . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit" class="btn-primary">Create Section</button>
                <a href="{{ route('admin.sections.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
