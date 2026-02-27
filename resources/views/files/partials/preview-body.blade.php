{{-- Reusable preview content: header + preview area. Used by drawer overlay and fixed panel. --}}
<div class="flex items-center justify-between px-4 py-3 border-b border-slate-200 bg-slate-50 shrink-0">
    <span class="font-semibold text-slate-800 truncate pr-2" x-text="selectedFile?.name"></span>
    <div class="flex items-center gap-2 shrink-0">
        <a :href="selectedFile ? '{{ url('/files') }}/' + selectedFile.id + '/preview' : '#'" target="_blank" rel="noopener" class="text-sm text-blue-600 hover:underline">Open</a>
        <a :href="selectedFile ? '{{ url('/files') }}/' + selectedFile.id + '/download' : '#'" class="text-sm text-slate-600 hover:underline">Download</a>
        <button @click="selectedFile = null" class="p-1.5 rounded-lg hover:bg-slate-200 text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
<div class="flex-1 flex flex-col min-h-0 overflow-hidden" x-show="selectedFile">
    <template x-if="selectedFile">
        <div class="flex-1 flex flex-col min-h-0 p-4 bg-slate-100">
            <div x-show="previewAsIframe(selectedFile.mime_type) && !previewAsImage(selectedFile.mime_type)" class="flex-1 min-h-0 rounded-lg overflow-hidden bg-white flex flex-col">
                <iframe :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" class="w-full flex-1 min-h-0 border-0" title="Preview"></iframe>
            </div>
            <div x-show="previewAsImage(selectedFile.mime_type)" class="flex-1 min-h-0 flex items-center justify-center rounded-lg overflow-hidden bg-white p-2">
                <img :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" :alt="selectedFile.name" class="max-w-full max-h-full w-auto h-auto object-contain">
            </div>
            <div x-show="previewAsAudio(selectedFile.mime_type)" class="flex flex-col items-center justify-center py-12">
                <audio :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" controls class="w-full max-w-lg"></audio>
                <p class="text-sm text-slate-500 mt-2" x-text="selectedFile.name"></p>
            </div>
            <div x-show="previewAsVideo(selectedFile.mime_type)" class="flex-1 min-h-0 flex items-center justify-center rounded-lg bg-black p-2" style="min-height: 280px;">
                <video :src="'{{ url('/files') }}/' + selectedFile.id + '/preview'" controls playsinline preload="metadata" class="w-full h-auto max-w-full max-h-full object-contain block"></video>
            </div>
            <div x-show="!previewable(selectedFile.mime_type)" class="flex-1 flex flex-col items-center justify-center py-16 text-slate-500">
                <svg class="w-20 h-20 text-slate-300 mb-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                <p class="text-sm mb-2">Preview not available</p>
                <a :href="'{{ url('/files') }}/' + selectedFile.id + '/download'" class="text-blue-600 hover:underline">Download</a>
            </div>
        </div>
    </template>
</div>
