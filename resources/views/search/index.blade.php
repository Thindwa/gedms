<x-app-layout>
    <x-slot name="header">Search</x-slot>

    <div class="max-w-3xl space-y-6">
        <form action="{{ route('search.index') }}" method="GET" class="card p-6">
            <div class="flex gap-3">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search files and documents..."
                       class="input-field flex-1">
                <button type="submit" class="btn-primary">Search</button>
            </div>
            <div class="mt-4 flex flex-wrap gap-4">
                <select name="type" class="input-field w-auto min-w-[120px]">
                    <option value="">All</option>
                    <option value="file" @selected(request('type') === 'file')>Files only</option>
                    <option value="document" @selected(request('type') === 'document')>Documents only</option>
                </select>
                <select name="status" class="input-field w-auto min-w-[120px]">
                    <option value="">Any status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                </select>
                <select name="document_type_id" class="input-field w-auto min-w-[140px]">
                    <option value="">Any type</option>
                    @foreach ($documentTypes as $dt)
                        <option value="{{ $dt->id }}" @selected(request('document_type_id') == $dt->id)>{{ $dt->name }}</option>
                    @endforeach
                </select>
                <input type="number" name="year" value="{{ request('year') }}" placeholder="Year" class="input-field w-24">
            </div>
        </form>

        @if (strlen(trim($query)) >= 2)
            <div class="card overflow-hidden">
                @forelse ($results as $item)
                    <a href="{{ $item['type'] === 'file' ? route('files.download', $item['id']) : route('documents.show', $item['id']) }}"
                       class="block p-5 border-b border-slate-100 last:border-0 hover:bg-slate-50/80 transition-colors">
                        <div class="font-medium text-slate-800 hover:text-slate-600">{{ $item['title'] }}</div>
                        <p class="mt-0.5 text-sm text-slate-500">
                            {{ $item['type'] === 'file' ? 'File' : 'Document' }}
                            · {{ $item['space'] ?? $item['document_type'] ?? '—' }}
                            @if ($item['type'] === 'document') · {{ $item['status'] }} @endif
                            · {{ $item['updated_at']->format('M j, Y') }}
                        </p>
                    </a>
                @empty
                    <div class="p-12 text-center text-slate-500">No results found.</div>
                @endforelse
            </div>
            <div class="mt-2">{{ $results->links() }}</div>
        @else
            <p class="text-slate-500">Enter at least 2 characters to search.</p>
        @endif
    </div>
</x-app-layout>
