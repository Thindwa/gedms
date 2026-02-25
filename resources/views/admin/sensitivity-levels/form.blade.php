@extends('layouts.app')
@section('content')
<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">{{ $level->exists ? 'Edit' : 'Create' }} Sensitivity Level</h1>
    <form action="{{ $level->exists ? route('admin.sensitivity-levels.update', $level) : route('admin.sensitivity-levels.store') }}" method="POST" class="card">
        <div class="card-body">
                @csrf
                @if ($level->exists) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name">Name</x-input-label>
                        <x-text-input id="name" name="name" value="{{ old('name', $level->name) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="code">Code</x-input-label>
                        <x-text-input id="code" name="code" value="{{ old('code', $level->code) }}" class="mt-1 block w-full" placeholder="internal" required />
                        <x-input-error :messages="$errors->get('code')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="sort_order">Sort Order</x-input-label>
                        <x-text-input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $level->sort_order ?? 0) }}" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn-primary">Save</button>
                    <a href="{{ route('admin.sensitivity-levels.index') }}" class="btn-secondary">Cancel</a>
                </div>
        </div>
    </form>
</div>
@endsection
