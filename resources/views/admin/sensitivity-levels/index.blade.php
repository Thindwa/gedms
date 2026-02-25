@extends('layouts.app')
@section('content')
<div class="flex justify-between items-center gap-4 mb-4">
    <h1 class="text-xl font-semibold text-slate-800">Sensitivity Levels</h1>
    <a href="{{ route('admin.sensitivity-levels.create') }}" class="btn-primary text-sm">+ Add Level</a>
</div>
<div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="card card-body bg-red-50 border-red-200 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="card overflow-hidden max-w-2xl">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($levels as $level)
                        <tr class="table-row">
                            <td>{{ $level->sort_order }}</td>
                            <td class="font-medium">{{ $level->name }}</td>
                            <td class="text-slate-500">{{ $level->code }}</td>
                            <td class="space-x-2">
                                <a href="{{ route('admin.sensitivity-levels.edit', $level) }}" class="text-slate-600 hover:text-slate-800 font-medium">Edit</a>
                                <form action="{{ route('admin.sensitivity-levels.destroy', $level) }}" method="POST" class="inline" onsubmit="return confirm('Delete this sensitivity level?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('admin.index') }}" class="text-sm text-slate-600 hover:text-slate-800">← Back to Admin</a>
</div>
@endsection
