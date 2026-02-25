@extends('layouts.app')
@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">{{ $rule->exists ? 'Edit' : 'Create' }} Retention Rule</h1>
    <form action="{{ $rule->exists ? route('admin.retention-rules.update', $rule) : route('admin.retention-rules.store') }}" method="POST" class="card">
        <div class="card-body">
                @csrf
                @if ($rule->exists) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <x-input-label for="document_type_id">Document Type</x-input-label>
                        <select id="document_type_id" name="document_type_id" class="mt-1 block w-full rounded border-gray-300" {{ $rule->exists ? 'disabled' : '' }} required>
                            @foreach ($documentTypes as $dt)
                                <option value="{{ $dt->id }}" @selected(old('document_type_id', $rule->document_type_id) == $dt->id)>{{ $dt->name }}</option>
                            @endforeach
                        </select>
                        @if ($rule->exists)
                            <input type="hidden" name="document_type_id" value="{{ $rule->document_type_id }}">
                        @endif
                    </div>
                    <div>
                        <x-input-label for="retention_years">Retention (years)</x-input-label>
                        <x-text-input id="retention_years" name="retention_years" type="number" min="0" max="100" value="{{ old('retention_years', $rule->retention_years ?? 7) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('retention_years')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="action">Action after retention</x-input-label>
                        <select id="action" name="action" class="mt-1 block w-full rounded border-gray-300">
                            <option value="archive" @selected(old('action', $rule->action ?? 'archive') === 'archive')>Archive</option>
                            <option value="dispose" @selected(old('action', $rule->action) === 'dispose')>Dispose</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="disposal_requires_approval" value="1" {{ old('disposal_requires_approval', $rule->disposal_requires_approval ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-600">Disposal requires approval</span>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn-primary">Save</button>
                    <a href="{{ route('admin.retention-rules.index') }}" class="btn-secondary">Cancel</a>
                </div>
        </div>
    </form>
</div>
@endsection
