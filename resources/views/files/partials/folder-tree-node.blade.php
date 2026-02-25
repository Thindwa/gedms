@php
    $isActive = $currentFolder && $currentFolder->id === $node['id'];
    $hasChildren = !empty($node['children']);
    $colorClass = $isActive ? 'text-white' : match (strlen($node['name']) % 4) {
        0 => 'text-blue-600',
        1 => 'text-emerald-600',
        2 => 'text-amber-600',
        default => 'text-sky-600',
    };
@endphp
<div class="pl-2">
    <a href="{{ route('files.index', ['space' => $spaceId ?? request()->get('space'), 'folder' => $node['id']]) }}"
       class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm {{ $isActive ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
        <svg class="w-4 h-4 shrink-0 {{ $isActive ? '' : $colorClass }}" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
        <span class="truncate">{{ $node['name'] }}</span>
    </a>
    @if ($hasChildren)
        @foreach ($node['children'] as $child)
            @include('files.partials.folder-tree-node', ['node' => $child, 'currentFolder' => $currentFolder, 'spaceId' => $spaceId ?? null])
        @endforeach
    @endif
</div>
