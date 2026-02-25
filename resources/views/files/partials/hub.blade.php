{{-- Department or Ministry hub: aggregated view, no creation --}}
<div class="flex-1 overflow-auto p-6">
    @if($space->type === \App\Models\StorageSpace::TYPE_DEPARTMENT)
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Sections in {{ $space->ownerDepartment?->name ?? 'Department' }}</h2>
        <p class="text-sm text-slate-500 mb-6">Select a section to browse its files and folders.</p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($hubSections as $section)
                @php $sectionSpace = $section->storageSpace; @endphp
                @if($sectionSpace)
                <a href="{{ route('files.index', ['space' => $sectionSpace->id]) }}" class="block p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/30 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <span class="font-medium text-slate-800">{{ $section->name }}</span>
                            <p class="text-xs text-slate-500 mt-0.5">Section space</p>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
        @if($hubSections->isEmpty())
            <p class="text-slate-500">No sections in this department.</p>
        @endif
    @else
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Departments in {{ $space->ownerMinistry?->name ?? 'Ministry' }}</h2>
        <p class="text-sm text-slate-500 mb-6">Select a department to view its sections.</p>
        <div class="space-y-6">
            @foreach($hubDepartments as $department)
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 font-medium text-slate-800">{{ $department->name }}</div>
                    <div class="p-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($department->sections as $section)
                            @php $sectionSpace = $section->storageSpace; @endphp
                            @if($sectionSpace)
                            <a href="{{ route('files.index', ['space' => $sectionSpace->id]) }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/20 transition-colors">
                                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
                                <span class="text-slate-800">{{ $section->name }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @if($hubDepartments->isEmpty())
            <p class="text-slate-500">No departments in this ministry.</p>
        @endif
    @endif
</div>
