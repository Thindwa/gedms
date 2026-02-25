<x-app-layout>
    <x-slot name="header">Promote to Official Document</x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-body">
                <p class="mb-6 text-slate-600">File: <strong class="text-slate-800">{{ $file->name }}</strong></p>

                <form action="{{ route('documents.promote', $file) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="title">Title *</x-input-label>
                        <x-text-input id="title" name="title" value="{{ old('title', $file->name) }}" class="input-field mt-1" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="document_type_id">Document Type *</x-input-label>
                        <select id="document_type_id" name="document_type_id" required class="input-field mt-1">
                            <option value="">Select...</option>
                            @foreach ($documentTypes as $dt)
                                <option value="{{ $dt->id }}" @selected(old('document_type_id') == $dt->id)>{{ $dt->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('document_type_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="ministry_id">Ministry *</x-input-label>
                        <select id="ministry_id" name="ministry_id" required class="input-field mt-1">
                            @foreach (auth()->user()->ministry ? [auth()->user()->ministry] : [] as $m)
                                <option value="{{ $m->id }}" @selected(old('ministry_id', auth()->user()->ministry_id) == $m->id)>{{ $m->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('ministry_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="department_id">Department *</x-input-label>
                        <select id="department_id" name="department_id" required class="input-field mt-1">
                            @foreach (auth()->user()->ministry?->departments ?? [] as $d)
                                <option value="{{ $d->id }}" @selected(old('department_id', auth()->user()->department_id) == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="owner_id">Owner *</x-input-label>
                        <select id="owner_id" name="owner_id" required class="input-field mt-1">
                            <option value="">Select...</option>
                            @foreach (\App\Models\User::where('ministry_id', auth()->user()->ministry_id)->where('is_active', true)->get() as $u)
                                <option value="{{ $u->id }}" @selected(old('owner_id', auth()->id()) == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('owner_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="sensitivity_level_id">Sensitivity Level *</x-input-label>
                        <select id="sensitivity_level_id" name="sensitivity_level_id" required class="input-field mt-1">
                            <option value="">Select...</option>
                            @foreach ($sensitivityLevels as $sl)
                                <option value="{{ $sl->id }}" @selected(old('sensitivity_level_id') == $sl->id)>{{ $sl->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sensitivity_level_id')" class="mt-1" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="requires_workflow" id="requires_workflow" value="1" {{ old('requires_workflow', true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <x-input-label for="requires_workflow" class="!mt-0">Require approval workflow</x-input-label>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="btn-primary">Promote</button>
                        <a href="{{ url()->previous() }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
