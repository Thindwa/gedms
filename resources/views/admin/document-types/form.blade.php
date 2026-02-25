@extends('layouts.app')
@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">{{ $documentType->exists ? 'Edit' : 'Create' }} Document Type</h1>
        <form action="{{ $documentType->exists ? route('admin.document-types.update', $documentType) : route('admin.document-types.store') }}" method="POST" class="card">
            <div class="card-body">
                @csrf
                @if ($documentType->exists) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input id="name" name="name" value="{{ old('name', $documentType->name) }}" class="input-field mt-1" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="code">Code</x-input-label>
                        <x-text-input id="code" name="code" value="{{ old('code', $documentType->code) }}" class="input-field mt-1" placeholder="POLICY" required />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="description">Description</x-input-label>
                        <textarea id="description" name="description" rows="2" class="input-field mt-1">{{ old('description', $documentType->description) }}</textarea>
                    </div>
                    @if (auth()->user()->can('manage-system'))
                    <div>
                        <x-input-label for="ministry_id">Ministry</x-input-label>
                        <select id="ministry_id" name="ministry_id" class="input-field mt-1">
                            <option value="">— All ministries —</option>
                            @foreach ($ministries as $m)
                                <option value="{{ $m->id }}" @selected(old('ministry_id', $documentType->ministry_id) == $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div>
                        <x-input-label for="workflow_definition_id">Workflow</x-input-label>
                        <select id="workflow_definition_id" name="workflow_definition_id" class="input-field mt-1">
                            <option value="">— None —</option>
                            @foreach ($workflows as $w)
                                <option value="{{ $w->id }}" @selected(old('workflow_definition_id', $documentType->workflow_definition_id) == $w->id)>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $documentType->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn-primary">Save</button>
                    <a href="{{ route('admin.document-types.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
</div>
@endsection
