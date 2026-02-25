@extends('layouts.app')

@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div>
        <h1 class="text-2xl font-semibold text-slate-800">Department Admin Dashboard</h1>
        <p class="text-slate-500 mt-0.5">{{ $dept->ministry?->name }} / {{ $dept->name }} — Admin Dashboard</p>
    </div>

    {{-- Overview cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.sections.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
            <div class="flex items-center justify-between">
                <span class="text-2xl font-bold text-amber-600">{{ $sectionsCount ?? 0 }}</span>
                <svg class="w-5 h-5 text-slate-400 group-hover:text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <p class="text-sm font-medium text-slate-600 mt-1">Sections</p>
        </a>
        <a href="{{ route('admin.users.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
            <span class="text-2xl font-bold text-emerald-600">{{ $activeUsersCount ?? 0 }}</span>
            <p class="text-sm font-medium text-slate-600 mt-1">Active Users</p>
        </a>
        <a href="{{ route('approvals.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
            <span class="text-2xl font-bold text-amber-600">{{ $pendingApprovalsCount ?? 0 }}</span>
            <p class="text-sm font-medium text-slate-600 mt-1">Pending Approvals</p>
        </a>
        <a href="{{ route('documents.index') }}" class="card p-6 hover:shadow-lg transition-shadow group">
            <span class="text-2xl font-bold text-sky-600">{{ $activeDocumentsCount ?? 0 }}</span>
            <p class="text-sm font-medium text-slate-600 mt-1">Active Documents</p>
        </a>
    </div>

</div>
@endsection
