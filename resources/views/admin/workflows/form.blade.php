@extends('layouts.app')
@section('content')
<div class="max-w-3xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">{{ $workflow->exists ? 'Edit' : 'Create' }} Workflow</h1>
    <form action="{{ $workflow->exists ? route('admin.workflows.update', $workflow) : route('admin.workflows.store') }}" method="POST" class="card">
        <div class="card-body">
                @csrf
                @if ($workflow->exists) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input id="name" name="name" value="{{ old('name', $workflow->name) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="description">Description</x-input-label>
                        <textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded border-gray-300">{{ old('description', $workflow->description) }}</textarea>
                    </div>
                    <div>
                        <x-input-label for="document_type_id">Document Type</x-input-label>
                        <select id="document_type_id" name="document_type_id" class="mt-1 block w-full rounded border-gray-300">
                            <option value="">— None —</option>
                            @foreach ($documentTypes as $dt)
                                <option value="{{ $dt->id }}" @selected(old('document_type_id', $workflow->document_type_id) == $dt->id)>{{ $dt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $workflow->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>

                    <div class="border-t pt-4">
                        <x-input-label>Steps (role-based approvals, in order)</x-input-label>
                        <p class="text-sm text-gray-500 mt-1">Leave name/role blank to remove a step.</p>
                        <div class="space-y-2 mt-2" id="steps-container">
                            @php
                                $stepIndex = 0;
                                $stepList = $steps->isEmpty() ? array_fill(0, 3, null) : array_merge($steps->all(), array_fill(0, max(0, 5 - $steps->count()), null));
                            @endphp
                            @foreach ($stepList as $step)
                                <div class="flex gap-2 items-center">
                                    <input type="hidden" name="steps[{{ $stepIndex }}][id]" value="{{ $step?->id }}">
                                    <input type="text" name="steps[{{ $stepIndex }}][name]" value="{{ old("steps.{$stepIndex}.name", $step?->name) }}" placeholder="Step name" class="flex-1 rounded border-gray-300 text-sm">
                                    <input type="text" name="steps[{{ $stepIndex }}][role_name]" value="{{ old("steps.{$stepIndex}.role_name", $step?->role_name) }}" placeholder="Role name (e.g. Chief Officer, Director)" class="flex-1 rounded border-gray-300 text-sm">
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="steps[{{ $stepIndex }}][is_parallel]" value="1" {{ old("steps.{$stepIndex}.is_parallel", $step?->is_parallel) ? 'checked' : '' }} class="rounded border-gray-300">
                                        <span class="ml-1">Parallel</span>
                                    </label>
                                </div>
                                @php $stepIndex++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn-primary">Save</button>
                    <a href="{{ route('admin.workflows.index') }}" class="btn-secondary">Cancel</a>
                </div>
        </div>
    </form>
</div>
@endsection
