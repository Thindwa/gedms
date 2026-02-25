<x-app-layout>
    <x-slot name="header">Memos</x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('memos.create') }}" class="btn-primary">+ New Memo</a>
            <a href="{{ route('memos.index', ['direction' => 'upward']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ ($direction ?? '') === 'upward' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Upward</a>
            <a href="{{ route('memos.index', ['direction' => 'downward']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ ($direction ?? '') === 'downward' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Downward</a>
            <a href="{{ route('memos.index', ['direction' => 'personal']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ ($direction ?? '') === 'personal' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Personal</a>
        </div>

        <div class="card overflow-hidden">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Title</th>
                        <th>Direction</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($memos as $m)
                        <tr class="table-row">
                            <td><a href="{{ route('memos.show', $m) }}" class="font-medium text-slate-800 hover:text-blue-600">{{ $m->title }}</a></td>
                            <td><span class="badge badge-gray">{{ ucfirst($m->direction) }}</span></td>
                            <td>{{ $m->toUser?->name ?? '—' }}</td>
                            <td><span class="badge {{ $m->status === 'sent' ? 'badge-blue' : ($m->status === 'acknowledged' ? 'badge-green' : 'badge-gray') }}">{{ ucfirst($m->status) }}</span></td>
                            <td class="text-slate-500">{{ $m->created_at->format('m.d.Y') }}</td>
                            <td><a href="{{ route('memos.show', $m) }}" class="text-blue-600 hover:underline text-sm">View</a></td>
                        </tr>
                    @empty
                        <tr><td></td><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No memos. <a href="{{ route('memos.create') }}" class="text-blue-600 hover:underline">Create one</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
