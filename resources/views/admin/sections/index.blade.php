@extends('layouts.app')

@section('content')
<div class="space-y-4">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-slate-800">Sections</h1>
        <a href="{{ route('admin.sections.create') }}" class="btn-primary text-sm">+ Add Section</a>
    </div>

    <div class="card overflow-hidden">
        <table class="min-w-full" data-datatable>
            <thead class="table-header">
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Ministry</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sections as $s)
                    <tr class="table-row">
                        <td class="font-medium">{{ $s->name }}</td>
                        <td class="text-slate-600">{{ $s->department?->name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $s->department?->ministry?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No sections.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ (Auth::user()?->can('manage-department') && !Auth::user()?->can('manage-system')) ? route('admin.department.index') : route('admin.index') }}" class="text-sm text-slate-600 hover:underline">← Back to Admin</a>
</div>
@endsection
