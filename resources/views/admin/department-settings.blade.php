@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Department Settings</h1>
        <p class="text-slate-500 mt-0.5">{{ $dept->ministry?->name }} / {{ $dept->name }}</p>
    </div>

    {{-- Drive Interface Style --}}
    <div class="card p-6">
        <h2 class="font-semibold text-slate-800 mb-2">Drive Interface Style</h2>
        <p class="text-sm text-slate-500 mb-4">Choose how the File Manager (Drive) appears for users in your department.</p>
        <form action="{{ route('admin.department.drive-style') }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            @php $current = in_array($dept->drive_style ?? '', ['drive','sharepoint','dropbox','nextcloud']) ? $dept->drive_style : 'nextcloud'; @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors {{ $current === 'nextcloud' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="drive_style" value="nextcloud" {{ $current === 'nextcloud' ? 'checked' : '' }} class="mt-1">
                    <div>
                        <span class="font-medium text-slate-800">Standard</span>
                        <p class="text-xs text-slate-500 mt-0.5">Left sidebar, breadcrumbs, grid/list, details panel. Clean, familiar layout.</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors {{ $current === 'sharepoint' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="drive_style" value="sharepoint" {{ $current === 'sharepoint' ? 'checked' : '' }} class="mt-1">
                    <div>
                        <span class="font-medium text-slate-800">Records & Compliance</span>
                        <p class="text-xs text-slate-500 mt-0.5">Fixed preview panel, detailed metadata. For formal records management.</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors {{ $current === 'drive' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="drive_style" value="drive" {{ $current === 'drive' ? 'checked' : '' }} class="mt-1">
                    <div>
                        <span class="font-medium text-slate-800">Structured</span>
                        <p class="text-xs text-slate-500 mt-0.5">Full folder tree, slide-out preview. Best for power users.</p>
                    </div>
                </label>
                <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-colors {{ $current === 'dropbox' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300' }}">
                    <input type="radio" name="drive_style" value="dropbox" {{ $current === 'dropbox' ? 'checked' : '' }} class="mt-1">
                    <div>
                        <span class="font-medium text-slate-800">Compact</span>
                        <p class="text-xs text-slate-500 mt-0.5">Collapsible sidebar, spacious cards. Modern, efficient layout.</p>
                    </div>
                </label>
            </div>
            <button type="submit" class="btn-primary">Save Drive Style</button>
        </form>
    </div>

    {{-- Mandatory Folders (per section) --}}
    <div class="card p-6">
        <h2 class="font-semibold text-slate-800 mb-2">Mandatory Folders (per Section)</h2>
        <p class="text-sm text-slate-500 mb-4">Select a section, add folder names (comma or newline separated), then save. Files and folders can only be created inside these folders. Department and Ministry spaces are read-only views—no creation there.</p>
        <form action="{{ route('admin.department.mandatory-folders') }}" method="POST" class="space-y-4"
            x-data="{
                sections: {{ json_encode($dept->sections()->orderBy('name')->get()->keyBy('id')->map(fn($s) => $s->getMandatoryFolderNames())->toArray()) }},
                selectedSectionId: null,
                bulkInput: '',
                addFolders() {
                    if (!this.selectedSectionId) return;
                    const names = this.bulkInput.split(/[,\n]+/).map(s => s.trim()).filter(Boolean);
                    if (!this.sections[this.selectedSectionId]) this.sections[this.selectedSectionId] = [];
                    names.forEach(n => {
                        if (!this.sections[this.selectedSectionId].includes(n)) this.sections[this.selectedSectionId].push(n);
                    });
                    this.bulkInput = '';
                },
                remove(sectionId, i) {
                    this.sections[sectionId].splice(i, 1);
                }
            }">
            @csrf
            @method('PUT')
            @php $sectionsList = $dept->sections()->orderBy('name')->get(); @endphp
            @if($sectionsList->isNotEmpty())
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Section</label>
                <select x-model="selectedSectionId" class="rounded-lg border-slate-300 w-full max-w-md">
                    <option value="">— Select section —</option>
                    @foreach($sectionsList as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="selectedSectionId">
                <label class="block text-sm font-medium text-slate-700 mb-2">Add folder names</label>
                <p class="text-xs text-slate-500 mb-1">Enter names separated by commas or new lines</p>
                <textarea x-model="bulkInput" rows="3" placeholder="e.g. Records, Projects, Archives" class="w-full rounded-lg border-slate-300 mb-2"></textarea>
                <button type="button" @click="addFolders()" class="btn-secondary mb-4">Add to section</button>
            </div>
            <div class="border-t border-slate-200 pt-4">
                <h3 class="text-sm font-medium text-slate-700 mb-2">Current configuration</h3>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($sectionsList as $s)
                    <div x-show="sections[{{ $s->id }}]?.length" class="text-sm">
                        <span class="font-medium text-slate-700">{{ $s->name }}:</span>
                        <span x-text="(sections[{{ $s->id }}] || []).join(', ')"></span>
                        <template x-for="(f, i) in (sections[{{ $s->id }}] || [])" :key="'{{ $s->id }}-'+i">
                            <input type="hidden" :name="'mandatory_folders[{{ $s->id }}][' + i + ']'" :value="f">
                        </template>
                        <button type="button" @click="sections[{{ $s->id }}] = []" class="ml-2 text-red-600 text-xs hover:underline">Clear</button>
                    </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn-primary">Save Mandatory Folders</button>
            @else
                <p class="text-slate-500 text-sm">No sections in this department. Add sections first.</p>
            @endif
        </form>
    </div>

    <div>
        <a href="{{ route('admin.department.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Dashboard</a>
    </div>
</div>
@endsection
