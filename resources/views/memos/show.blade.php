<x-app-layout>
    <x-slot name="header">{{ $memo->title }}</x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="card">
            <dl class="p-6 space-y-3">
                <div><dt class="text-xs text-slate-500">From</dt><dd class="font-medium">{{ $memo->fromUser->name }}</dd></div>
                <div><dt class="text-xs text-slate-500">To</dt><dd class="font-medium">{{ $memo->toUser?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-500">Direction</dt><dd class="font-medium">{{ ucfirst($memo->direction) }}</dd></div>
                <div><dt class="text-xs text-slate-500">Status</dt><dd><span class="badge {{ $memo->status === 'sent' ? 'badge-blue' : ($memo->status === 'acknowledged' ? 'badge-green' : 'badge-gray') }}">{{ ucfirst($memo->status) }}</span></dd></div>
                <div><dt class="text-xs text-slate-500">Requires Approval</dt><dd>{{ $memo->requires_approval ? 'Yes' : 'No' }}</dd></div>
                <div><dt class="text-xs text-slate-500">Body</dt><dd class="whitespace-pre-wrap text-slate-700">{{ $memo->body ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="flex gap-2">
            @if ($memo->status === 'draft' && $memo->from_user_id === auth()->id())
                <form action="{{ route('memos.send', $memo) }}" method="POST">@csrf<button type="submit" class="btn-primary">Send</button></form>
            @endif
            @if ($memo->status === 'sent' && $memo->to_user_id === auth()->id())
                <form action="{{ route('memos.acknowledge', $memo) }}" method="POST">@csrf<button type="submit" class="btn-primary">Acknowledge</button></form>
            @endif
            <a href="{{ route('memos.index') }}" class="btn-secondary">Back to Memos</a>
        </div>
    </div>
</x-app-layout>
