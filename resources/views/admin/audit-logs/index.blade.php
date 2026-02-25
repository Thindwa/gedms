@extends('layouts.app')
@section('content')
<div class="space-y-4">
    <h1 class="text-xl font-semibold text-slate-800 mb-4">Audit Logs</h1>
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="card p-5 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Action</label>
                <select name="action" class="input-field w-auto min-w-[140px]">
                    <option value="">All</option>
                    @foreach ($actions as $a)
                        <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">From date</label>
                <input type="date" name="from" value="{{ request('from') }}" class="input-field w-auto">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">To date</label>
                <input type="date" name="to" value="{{ request('to') }}" class="input-field w-auto">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Subject type</label>
                <input type="text" name="subject_type" value="{{ request('subject_type') }}" placeholder="e.g. Document" class="input-field w-48">
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full" data-datatable>
                    <thead class="table-header">
                        <tr>
                            <th>Time</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="table-row">
                                <td class="whitespace-nowrap text-slate-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                                <td class="font-medium">{{ $log->action }}</td>
                                <td class="text-slate-600">{{ $log->user?->email ?? $log->user_email ?? '—' }}</td>
                                <td class="text-slate-600">{{ $log->subject_type ? class_basename($log->subject_type) . '#' . $log->subject_id : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No audit logs.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to Admin</a>
</div>
@endsection
