<x-app-layout>
    <x-slot name="header">Official Documents</x-slot>

    <div class="space-y-4">
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        <form action="{{ route('documents.index') }}" method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="under_review" @selected(request('status') === 'under_review')>Under Review</option>
                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                <option value="archived" @selected(request('status') === 'archived')>Archived</option>
            </select>
            <select name="document_type_id" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
                <option value="">All types</option>
                @foreach ($documentTypes as $dt)
                    <option value="{{ $dt->id }}" @selected(request('document_type_id') == $dt->id)>{{ $dt->name }}</option>
                @endforeach
            </select>
            <select name="department_id" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
                <option value="">All departments</option>
                @foreach ($departments ?? [] as $d)
                    <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
            <select name="sensitivity_level_id" class="input-field w-auto min-w-[140px]" onchange="this.form.submit()">
                <option value="">All sensitivity</option>
                @foreach ($sensitivityLevels ?? [] as $sl)
                    <option value="{{ $sl->id }}" @selected(request('sensitivity_level_id') == $sl->id)>{{ $sl->name }}</option>
                @endforeach
            </select>
        </form>

        <div class="card overflow-hidden">
            <table class="min-w-full" data-datatable>
                <thead class="table-header">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Owner</th>
                        <th>Sensitivity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $doc)
                        <tr class="table-row">
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="font-medium text-slate-800 hover:text-slate-600">{{ $doc->title }}</a>
                            </td>
                            <td>{{ $doc->documentType->name }}</td>
                            <td>
                                <span class="badge {{ $doc->status === 'draft' ? 'badge-gray' : ($doc->status === 'under_review' ? 'badge-yellow' : ($doc->status === 'approved' ? 'badge-green' : 'badge-gray')) }}">
                                    {{ str_replace('_', ' ', ucfirst($doc->status)) }}
                                </span>
                            </td>
                            <td>{{ $doc->owner->name }}</td>
                            <td>{{ $doc->sensitivityLevel->name }}</td>
                            <td>
                                <a href="{{ route('documents.show', $doc) }}" class="font-medium text-slate-600 hover:text-slate-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td></td><td></td><td></td><td></td><td></td><td class="px-6 py-12 text-center text-slate-500">No documents. Promote a file from the File Manager.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
