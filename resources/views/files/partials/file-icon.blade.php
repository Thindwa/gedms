@php
$ext = strtolower(pathinfo($file->name ?? '', PATHINFO_EXTENSION));
$iconType = match(true) {
    in_array($ext, ['pdf']) => 'pdf',
    in_array($ext, ['doc', 'docx']) => 'word',
    in_array($ext, ['xls', 'xlsx', 'ods']) => 'excel',
    in_array($ext, ['ppt', 'pptx', 'odp']) => 'presentation',
    in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'tiff']) => 'image',
    in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v', 'wmv']) => 'video',
    in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'flac', 'aac']) => 'audio',
    in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2']) => 'archive',
    in_array($ext, ['txt', 'csv', 'json', 'xml', 'md', 'log', 'ini']) => 'text',
    default => 'document',
};
$iconColor = match($iconType) {
    'pdf' => 'text-red-500',
    'word' => 'text-blue-600',
    'excel' => 'text-emerald-600',
    'presentation' => 'text-amber-600',
    'image' => 'text-violet-500',
    'video' => 'text-rose-500',
    'audio' => 'text-teal-500',
    'archive' => 'text-amber-700',
    'text' => 'text-slate-600',
    default => 'text-slate-500',
};
$sizeClass = $size ?? 'w-5 h-5';
@endphp
<span class="{{ $sizeClass }} {{ $iconColor }} shrink-0 inline-flex items-center justify-center" title="{{ $ext ? strtoupper($ext) . ' file' : 'File' }}">
    @if($iconType === 'pdf')
    {{-- PDF document --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
    @elseif($iconType === 'word')
    {{-- Word document --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
    @elseif($iconType === 'excel')
    {{-- Spreadsheet --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/></svg>
    @elseif($iconType === 'presentation')
    {{-- Presentation / slides --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm3 2h6v4H7V5zm8 8v2h1v-2h-1zm-2-2H7v4h6v-4zm2 0h1V9h-1v2zm1-4V5h-1v2h1zM5 5v2H4V5h1zm0 4H4v2h1V9zm-1 4h1v2H4v-2z" clip-rule="evenodd"/></svg>
    @elseif($iconType === 'image')
    {{-- Image / photo --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm4 3a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H7a1 1 0 01-1-1V8z" clip-rule="evenodd"/></svg>
    @elseif($iconType === 'video')
    {{-- Video --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/></svg>
    @elseif($iconType === 'audio')
    {{-- Audio --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.617.076L4 12H2a1 1 0 01-1-1V9a1 1 0 011-1h2l4.383-4.924zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/></svg>
    @elseif($iconType === 'archive')
    {{-- Archive --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V7.414A2 2 0 014 7.414V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
    @elseif($iconType === 'text')
    {{-- Text --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
    @else
    {{-- Default document --}}
    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
    @endif
</span>
