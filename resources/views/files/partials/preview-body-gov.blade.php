{{-- Government-style preview header + content (mockup: Budget Report with metadata & version history) --}}
<div class="gov-drive-preview-header flex items-center justify-between shrink-0 border-b border-slate-700/50">
    <span class="font-semibold text-white/95 truncate pr-2 flex items-center gap-2">
        <form :action="selectedFile ? '{{ url('/files') }}/' + selectedFile.id + '/favorite' : '#'" method="POST" class="inline shrink-0" x-show="selectedFile">
            @csrf
            <button type="submit" class="p-1 rounded hover:bg-white/10 text-white/80 hover:text-amber-300" :title="isFavorited(selectedFile?.id) ? 'Remove from favorites' : 'Add to favorites'">
                <svg class="w-5 h-5" :class="isFavorited(selectedFile?.id) ? 'fill-amber-400 text-amber-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </button>
        </form>
        <span x-text="selectedFile?.name"></span>
    </span>
    <div class="flex items-center gap-2 shrink-0">
        <button @click="previewSidebarOpen = !previewSidebarOpen" class="p-1.5 rounded hover:bg-white/10 text-white/80 hover:text-white transition-colors" :title="previewSidebarOpen ? 'Hide details' : 'Show details'">
            <svg class="w-5 h-5" :class="previewSidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </button>
        <a :href="selectedFile ? '{{ url('/files') }}/' + selectedFile.id + '/preview' : '#'" target="_blank" rel="noopener" class="text-sm text-white/80 hover:text-white transition-colors">Open</a>
        <a :href="selectedFile ? '{{ url('/files') }}/' + selectedFile.id + '/download' : '#'" class="text-sm text-white/80 hover:text-white transition-colors">Download</a>
        <template x-if="selectedFile && selectedFile.isLockedByMe">
            <form :action="'{{ url('/files') }}/' + selectedFile.id + '/unlock'" method="POST" class="inline" @click.stop>
                @csrf
                <button type="submit" class="text-sm text-white/80 hover:text-white transition-colors">Unlock</button>
            </form>
        </template>
        <template x-if="selectedFile && !selectedFile.isLockedByMe && !selectedFile.locked_by">
            <form :action="'{{ url('/files') }}/' + selectedFile.id + '/lock'" method="POST" class="inline" @click.stop>
                @csrf
                <button type="submit" class="text-sm text-white/80 hover:text-white transition-colors">Lock</button>
            </form>
        </template>
        <button @click="selectedFile = null" class="p-1.5 rounded hover:bg-white/10 text-white/80 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
<div class="flex-1 flex min-h-0 overflow-hidden" x-show="selectedFile">
    <template x-if="selectedFile">
        {{-- Horizontal flex: preview (primary) and sidebar (secondary) as siblings. Independent scroll containers. --}}
        <div class="flex-1 flex flex-row min-h-0 min-w-0 w-full">
            {{-- Preview: flex 1, min-width 0, its own overflow. Primary surface. --}}
            <div class="flex-1 min-w-0 min-h-0 flex flex-col bg-slate-100 overflow-auto">
                <template x-if="previewAsIframe(selectedFile.mime_type) && !previewAsImage(selectedFile.mime_type)">
                <div class="flex-1 min-h-0 min-w-0 flex flex-col p-4">
                    <div class="flex-1 min-h-0 min-w-0 rounded-lg bg-white shadow border border-slate-200 overflow-hidden" style="min-height: 400px;">
                        <iframe :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" class="w-full h-full border-0 block" style="width: 100%; height: 100%; min-height: 400px;" title="PDF Preview"></iframe>
                    </div>
                </div>
                </template>
                <template x-if="previewAsImage(selectedFile.mime_type)">
                <div class="flex-1 min-h-0 min-w-0 flex items-center justify-center p-4 overflow-auto" x-data="{ imgError: false }" x-effect="imgError = false">
                    <div class="rounded-lg bg-white border border-slate-200 shadow-sm p-4">
                        <img x-show="!imgError" :src="'{{ url('/files') }}/' + selectedFile.id + '/preview?v=' + (selectedFile.version || 1)"
                             :alt="selectedFile.name"
                             class="max-w-full max-h-[70vh] w-auto h-auto object-contain"
                             loading="eager"
                             @@error="imgError = true">
                        <p x-show="imgError" class="text-sm text-amber-600 text-center py-4">Preview could not be loaded. <a :href="'{{ url('/files') }}/' + selectedFile.id + '/download'" class="text-indigo-600 hover:underline">Download</a></p>
                    </div>
                </div>
                </template>
                <template x-if="previewAsAudio(selectedFile.mime_type)">
                <div class="flex-1 flex flex-col items-center justify-center p-4 overflow-auto">
                    <div class="rounded-lg bg-white border border-slate-200 shadow-sm p-6 max-w-md w-full">
                        <audio :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" controls class="w-full"></audio>
                        <p class="text-sm text-slate-500 mt-2 truncate" x-text="selectedFile.name"></p>
                    </div>
                </div>
                </template>
                <template x-if="previewAsVideo(selectedFile.mime_type)">
                <div class="flex-1 min-h-0 min-w-0 flex flex-col items-center justify-center p-4 overflow-auto" x-data="{ vol: 1, muted: false }" x-init="vol = 1; muted = false">
                    <div class="rounded-lg bg-slate-900 border border-slate-200 shadow-sm w-full flex flex-col" style="max-height: calc(100vh - 10rem); min-height: 320px;">
                        <video x-ref="videoEl"
                               :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'"
                               controls
                               playsinline
                               preload="metadata"
                               class="w-full h-auto object-contain block"
                               style="max-height: calc(100vh - 14rem);"
                               @loadedmetadata="$refs.videoEl.volume = vol; $refs.videoEl.muted = muted"
                               @volumechange="vol = $refs.videoEl.volume; muted = $refs.videoEl.muted"></video>
                        <div class="flex items-center gap-3 px-4 py-2 bg-slate-800/80 border-t border-slate-700">
                            <button type="button" @click="muted = !muted; $refs.videoEl.muted = muted" class="text-white/90 hover:text-white" :title="muted ? 'Unmute' : 'Mute'">
                                <svg x-show="!muted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                <svg x-show="muted" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                            </button>
                            <input type="range" min="0" max="1" step="0.05" x-model.number="vol"
                                   @input="const v = parseFloat(vol); $refs.videoEl.volume = v; if (v > 0) { muted = false; $refs.videoEl.muted = false }"
                                   class="w-24 h-1.5 accent-blue-500 cursor-pointer">
                        </div>
                    </div>
                </div>
                </template>
                <template x-if="!previewable(selectedFile.mime_type)">
                <div class="flex-1 flex flex-col items-center justify-center p-4 overflow-auto text-slate-500">
                    <div class="rounded-lg border border-slate-200 bg-white p-8 text-center shadow-sm">
                        <svg class="w-16 h-16 text-slate-300 mb-4 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                        <p class="text-sm font-medium text-slate-600">Preview not available</p>
                        <a :href="'{{ url('/files') }}/' + selectedFile.id + '/download'" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-700 font-medium">Download file</a>
                    </div>
                </div>
                </template>
            </div>

            {{-- Sidebar: collapsible, fixed width when open. Reflows preview when toggled. --}}
            <aside x-show="previewSidebarOpen" x-cloak
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 -translate-x-4"
                   x-transition:enter-end="opacity-100 translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 translate-x-0"
                   x-transition:leave-end="opacity-0 -translate-x-4"
                   class="w-64 flex-shrink-0 flex flex-col border-l-2 border-slate-400 bg-white overflow-y-auto min-h-0">
            <div class="p-4 border-b border-slate-200">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Details</h4>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2"><dt class="text-slate-500 shrink-0">Type</dt><dd class="text-slate-800 text-right truncate" x-text="selectedFile?.doc_type ?? selectedFile?.mime_type ?? '—'"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500 shrink-0">Ministry</dt><dd class="text-slate-800 text-right truncate" x-text="selectedFile?.ministry ?? '—'"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500 shrink-0">Department</dt><dd class="text-slate-800 text-right truncate" x-text="selectedFile?.department ?? '—'"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500 shrink-0">Owner</dt><dd class="text-slate-800 text-right truncate" x-text="selectedFile?.owner ?? '—'"></dd></div>
                    <div class="flex justify-between gap-2"><dt class="text-slate-500 shrink-0">Sensitivity</dt><dd class="text-slate-800 text-right truncate" x-text="selectedFile?.sensitivity ?? 'Internal'"></dd></div>
                </dl>
            </div>
            <div class="p-4 border-b border-slate-200">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Version History</h4>
                <div class="space-y-2" x-show="selectedFile?.versions?.length">
                    <template x-for="(v, i) in (selectedFile?.versions || [])" :key="i">
                        <div class="flex items-center justify-between py-1.5 px-2 rounded bg-white border border-slate-100 text-sm">
                            <div>
                                <span class="font-medium text-slate-800" x-text="'v' + v.version"></span>
                                <span class="text-slate-500 ml-2" x-text="v.creator || '—'"></span>
                            </div>
                            <span class="text-xs text-slate-400" x-text="v.date"></span>
                        </div>
                    </template>
                </div>
                <p x-show="!selectedFile?.versions?.length" class="text-sm text-slate-400">No version history</p>
            </div>

            <div class="p-4 border-b border-slate-200" x-data="{ tagIds: selectedFile?.tags?.map(t => t.id) || [], availableTags: @json($tags ?? []), newTag: '' }" x-effect="tagIds = selectedFile?.tags?.map(t => t.id) || []">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Tags</h4>
                <div class="flex flex-wrap gap-2 mb-2">
                    <template x-for="t in (selectedFile?.tags || [])" :key="t.id">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" :style="'background-color:' + (t.color || '#64748b') + '20; color:' + (t.color || '#64748b')" x-text="t.name"></span>
                    </template>
                </div>
                <div class="flex flex-wrap gap-1 mb-2" x-show="availableTags.length">
                    <template x-for="tag in availableTags" :key="tag.id">
                        <button type="button" @click="fetch('{{ url('/files') }}/' + selectedFile.id + '/tags', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'Accept': 'application/json' }, body: JSON.stringify({ tag_ids: (tagIds.includes(tag.id) ? tagIds.filter(i => i !== tag.id) : [...tagIds, tag.id]) }) }).then(r => r.json()).then(d => { selectedFile.tags = d.tags; tagIds = d.tags.map(t => t.id); })"
                            class="px-2 py-1 rounded text-xs border" :class="(selectedFile?.tags || []).some(t => t.id === tag.id) ? 'border-slate-300 bg-slate-100' : 'border-slate-200 hover:bg-slate-50'"
                            x-text="tag.name"></button>
                    </template>
                </div>
                <form @submit.prevent="const n = newTag.trim(); if (n) { fetch('{{ url('/files') }}/' + selectedFile.id + '/tags', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'Accept': 'application/json' }, body: JSON.stringify({ tag_ids: tagIds, tag_names: [n] }) }).then(r => r.json()).then(d => { selectedFile.tags = d.tags; tagIds = d.tags.map(t => t.id); newTag = ''; const added = d.tags.find(t => t.name === n); if (added && !availableTags.find(t => t.id === added.id)) availableTags = [...availableTags, added]; }); }" class="flex gap-2">
                    <input type="text" x-model="newTag" placeholder="Add tag…" class="flex-1 px-3 py-1.5 text-sm border border-slate-200 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="px-2 py-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">Add</button>
                </form>
            </div>

            <div class="p-4" x-data="{ comments: [], loading: false }" x-init="$watch('selectedFile', async f => { if (f?.id) { loading = true; const r = await fetch('{{ url('/files') }}/' + f.id + '/comments'); comments = await r.json(); loading = false; } else { comments = []; } })">
                <h4 class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Comments</h4>
                <div class="space-y-2 mb-3 max-h-32 overflow-y-auto">
                    <template x-for="c in comments" :key="c.id">
                        <div class="py-2 px-3 rounded bg-white border border-slate-100 text-sm">
                            <p class="text-slate-800" x-text="c.body"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="c.user_name + ' · ' + c.created_at"></p>
                        </div>
                    </template>
                    <p x-show="!loading && comments.length === 0" class="text-sm text-slate-400">No comments</p>
                </div>
                <form @submit.prevent="fetch('{{ url('/files') }}/' + selectedFile.id + '/comments', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', 'Accept': 'application/json' }, body: JSON.stringify({ body: $refs.commentInput.value }) }).then(r => r.json()).then(c => { comments.push(c); $refs.commentInput.value = ''; })"
                    class="flex gap-2">
                    <input type="text" x-ref="commentInput" placeholder="Add a comment…" class="flex-1 px-3 py-2 text-sm border border-slate-200 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Add</button>
                </form>
            </div>
            </aside>
        </div>
    </template>
</div>
