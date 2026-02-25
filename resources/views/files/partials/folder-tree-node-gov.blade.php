@php
    $isActive = $currentFolder && $currentFolder->id === $node['id'];
    $hasChildren = !empty($node['children']);
@endphp
<div class="pl-1">
    <a href="{{ route('files.index', ['space' => $spaceId ?? request()->get('space'), 'folder' => $node['id']]) }}"
       class="gov-drive-tree-link {{ $isActive ? 'active' : 'text-slate-700' }}">
        <svg class="w-4 h-4 shrink-0 {{ $isActive ? 'text-white/90' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>
        <span class="truncate">{{ $node['name'] }}</span>
    </a>
    @if ($hasChildren)
        <div class="ml-3 mt-0.5 border-l border-slate-200 pl-2 space-y-0.5">
            @foreach ($node['children'] as $child)
                @include('files.partials.folder-tree-node-gov', ['node' => $child, 'currentFolder' => $currentFolder, 'spaceId' => $spaceId ?? null])
            @endforeach
        </div>
    @endif
</div>
