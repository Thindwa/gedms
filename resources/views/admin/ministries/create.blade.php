@extends('layouts.app')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">Add Ministry</h1>

    <form action="{{ route('admin.ministries.store') }}" method="POST" class="card">
        <div class="card-body">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ministry Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="input-field" placeholder="e.g. Ministry of Finance">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Code (optional)</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}"
                           class="input-field" placeholder="e.g. MOF">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description (optional)</label>
                    <textarea id="description" name="description" rows="2" class="input-field" placeholder="Brief description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex gap-2">
                <button type="submit" class="btn-primary">Create Ministry</button>
                <a href="{{ route('admin.ministries.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
