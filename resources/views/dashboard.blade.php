<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="space-y-6">
        <h1 class="text-xl font-semibold text-slate-800">Welcome back, {{ auth()->user()->name }}.</h1>
        @if (session('success'))
            <div class="card card-body bg-emerald-50 border-emerald-200 text-emerald-800">{{ session('success') }}</div>
        @endif

        {{-- Role-based dashboard (mockup style) --}}
        <div class="card overflow-hidden" x-data="{ activeTab: 0 }">
            <div class="flex border-b border-slate-200 bg-slate-50">
                @foreach ($tabs ?? ['Overview'] as $i => $tab)
                    <button @click="activeTab = {{ $i }}"
                            :class="activeTab === {{ $i }} ? 'border-blue-600 text-blue-700 font-medium' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            class="px-6 py-3 text-sm border-b-2 transition-colors">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>
            <div class="p-6">
                @php $tab = $tabMap ?? []; @endphp
                {{-- Officer: My Files --}}
                @if (isset($tab['files']))
                    <div class="space-y-2" x-show="activeTab === {{ $tab['files'] }}" x-cloak x-transition>
                        <h3 class="font-semibold text-slate-800 mb-3">My Files</h3>
                        @if (isset($content['files']) && $content['files']->isNotEmpty())
                            @foreach ($content['files'] as $f)
                                <a href="{{ route('files.index', array_filter(['space' => $f->storage_space_id, 'folder' => $f->folder_id, 'file' => $f->id], fn($v) => $v !== null)) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                    <svg class="w-8 h-8 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $f->name }}</div>
                                        <div class="text-sm text-slate-500">{{ $f->updated_at->format('m.d.Y') }}</div>
                                    </div>
                                </a>
                            @endforeach
                            <a href="{{ route('files.index') }}" class="inline-block text-sm text-slate-600 hover:underline mt-2">View all files →</a>
                        @else
                            <p class="text-slate-500 py-4">No files yet. <a href="{{ route('files.index') }}" class="text-blue-600 hover:underline">Go to Drive</a> to upload.</p>
                        @endif
                    </div>
                @endif

                {{-- Drafts --}}
                @if (isset($tab['drafts']))
                    <div class="space-y-2" x-show="activeTab === {{ $tab['drafts'] }}" x-cloak x-transition>
                        <h3 class="font-semibold text-slate-800 mb-3">Drafts</h3>
                        @if (isset($content['drafts']) && $content['drafts']->isNotEmpty())
                            @foreach ($content['drafts'] as $d)
                                <a href="{{ route('documents.show', $d) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                    <svg class="w-8 h-8 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $d->title }}</div>
                                        <div class="text-sm text-slate-500">{{ $d->updated_at->format('m.d.Y') }}</div>
                                    </div>
                                </a>
                            @endforeach
                            <a href="{{ route('documents.index', ['status' => 'draft']) }}" class="inline-block text-sm text-slate-600 hover:underline mt-2">View all drafts →</a>
                        @else
                            <p class="text-slate-500 py-4">No drafts. <a href="{{ route('documents.index') }}" class="text-blue-600 hover:underline">Create a document</a> or promote a file from Drive.</p>
                        @endif
                    </div>
                @endif

                {{-- My Tasks (Officer: documents needing action + memos) --}}
                @if (isset($tab['tasks']))
                    <div class="space-y-2" x-show="activeTab === {{ $tab['tasks'] }}" x-cloak x-transition>
                        @if (isset($content['tasks']) && $content['tasks']->isNotEmpty())
                            <h3 class="font-semibold text-slate-800 mb-3">My Tasks</h3>
                            @foreach ($content['tasks'] as $t)
                                <a href="{{ route('documents.show', $t) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                    <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $t->title }}</div>
                                        <div class="text-sm text-slate-500">{{ $t->documentType?->name ?? $t->status }} · {{ $t->updated_at->format('m.d.Y') }}</div>
                                    </div>
                                </a>
                            @endforeach
                            <a href="{{ route('documents.index') }}" class="inline-block text-sm text-slate-600 hover:underline mt-2">View all documents →</a>
                        @endif

                {{-- Personal Memos (in My Tasks tab) --}}
                        @if (isset($content['memos']) && $content['memos']->isNotEmpty())
                            <div class="{{ (isset($content['tasks']) && $content['tasks']->isNotEmpty()) ? 'mt-6' : '' }}">
                                <h3 class="font-semibold text-slate-800 mb-3">Personal Memos</h3>
                                @foreach ($content['memos'] as $m)
                                    <a href="{{ route('memos.show', $m) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                        <svg class="w-8 h-8 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                        <div>
                                            <div class="font-medium text-slate-800">{{ $m->title }}</div>
                                            <div class="text-sm text-slate-500">{{ $m->direction }} · {{ $m->created_at->format('m.d.Y') }}</div>
                                        </div>
                                    </a>
                                @endforeach
                                <a href="{{ route('memos.index') }}" class="inline-block text-sm text-slate-600 hover:underline mt-2">View all memos →</a>
                            </div>
                        @endif

                        @if ((!isset($content['tasks']) || $content['tasks']->isEmpty()) && (!isset($content['memos']) || $content['memos']->isEmpty()))
                            <p class="text-slate-500 py-4">No tasks or memos. Documents in draft or under review will appear here.</p>
                        @endif
                    </div>
                @endif

                {{-- Approvers: Tab 0 placeholder (Section/Department/Ministry files) --}}
                @if (isset($tab['scopeFiles']) && isset($tab['approvals']))
                    <div class="space-y-4" x-show="activeTab === {{ $tab['scopeFiles'] }}" x-cloak x-transition>
                        <p class="text-slate-600">View files in your scope via Drive or EDMS.</p>
                        <a href="{{ route('files.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Go to Drive</a>
                        <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 ml-2">EDMS</a>
                    </div>
                @endif

                {{-- Pending Approvals (Chief Officer, Director, PS, Minister) --}}
                @if (isset($content['approvals']) && $content['approvals']->isNotEmpty())
                    <div class="space-y-2" x-show="activeTab === {{ $tab['approvals'] ?? -1 }}" x-cloak x-transition>
                        <h3 class="font-semibold text-slate-800 mb-3">Pending Approvals</h3>
                        @foreach ($content['approvals'] as $step)
                            @php $doc = $step->workflowInstance->document; @endphp
                            <a href="{{ route('documents.show', $doc) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                <svg class="w-8 h-8 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                <div>
                                    <div class="font-medium text-slate-800">{{ $doc->title }}</div>
                                    <div class="text-sm text-slate-500">{{ $step->workflowStep?->name }} · {{ $doc->documentType?->name }}</div>
                                </div>
                            </a>
                        @endforeach
                        <a href="{{ route('approvals.index') }}" class="inline-block text-sm text-slate-600 hover:underline mt-2">View approval queue →</a>
                    </div>
                @endif

                {{-- Records Officer: Retention tab placeholder --}}
                @if (isset($tab['retention']))
                    <div class="space-y-4" x-show="activeTab === {{ $tab['retention'] }}" x-cloak x-transition>
                        <p class="text-slate-600">Retention rules and disposition schedules are configured in Administration.</p>
                        <a href="{{ route('admin.retention-rules.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Retention Rules</a>
                    </div>
                @endif

                {{-- Records Officer: Legal Hold, Archived --}}
                @if (isset($content['legalHold']) && $content['legalHold']->isNotEmpty())
                    <div class="space-y-2" x-show="activeTab === {{ $tab['legalHold'] ?? -1 }}" x-cloak x-transition>
                        <h3 class="font-semibold text-slate-800 mb-3">Legal Hold</h3>
                        @foreach ($content['legalHold'] as $d)
                            <a href="{{ route('documents.show', $d) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                <span class="badge-red">Hold</span>
                                <div>
                                    <div class="font-medium text-slate-800">{{ $d->title }}</div>
                                    <div class="text-sm text-slate-500">{{ $d->documentType?->name }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if (isset($content['archived']) && $content['archived']->isNotEmpty())
                    <div class="space-y-2" x-show="activeTab === {{ $tab['archived'] ?? -1 }}" x-cloak x-transition>
                        <h3 class="font-semibold text-slate-800 mb-3">Archived</h3>
                        @foreach ($content['archived'] as $d)
                            <a href="{{ route('documents.show', $d) }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50">
                                <svg class="w-8 h-8 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <div>
                                    <div class="font-medium text-slate-800">{{ $d->title }}</div>
                                    <div class="text-sm text-slate-500">{{ $d->updated_at->format('m.d.Y') }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Auditor --}}
                @if (isset($content['auditOnly']) && $content['auditOnly'])
                    <div class="space-y-4" x-show="activeTab === {{ $tab['auditOnly'] ?? 0 }}" x-cloak x-transition>
                        <p class="text-slate-600">Read-only access to audit logs.</p>
                        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">View Audit Logs</a>
                    </div>
                    @if (isset($tab['auditorApprovals']))
                    <div class="space-y-4" x-show="activeTab === {{ $tab['auditorApprovals'] }}" x-cloak x-transition>
                        <p class="text-slate-600">Approval and workflow activity is recorded in the Audit Logs.</p>
                        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">View Audit Logs</a>
                    </div>
                    @endif
                @endif

                {{-- Default / empty (fallback when no role match) --}}
                @if (isset($tab['overview']) && (empty($content) || (empty($content['files'] ?? null) && empty($content['drafts'] ?? null) && empty($content['tasks'] ?? null) && empty($content['memos'] ?? null) && empty($content['approvals'] ?? null) && empty($content['legalHold'] ?? null) && empty($content['archived'] ?? null) && empty($content['auditOnly'] ?? null))))
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-show="activeTab === {{ $tab['overview'] ?? 0 }}" x-cloak x-transition>
                        <a href="{{ route('files.index') }}" class="card p-6 hover:shadow-card-hover transition-shadow group">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-slate-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-800">File Manager</h3>
                                    <p class="text-sm text-slate-500">Working files</p>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('documents.index') }}" class="card p-6 hover:shadow-card-hover transition-shadow group">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-slate-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-800">Documents</h3>
                                    <p class="text-sm text-slate-500">Official records</p>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('search.index') }}" class="card p-6 hover:shadow-card-hover transition-shadow group">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-slate-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-800">Search</h3>
                                    <p class="text-sm text-slate-500">Find files</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Profile info --}}
        <div class="card overflow-hidden">
            <div class="card-body border-b border-slate-200/80 bg-slate-50/50">
                <h2 class="page-title">Your profile</h2>
            </div>
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                <div class="px-6 py-4">
                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Ministry</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ auth()->user()->ministry?->name ?? '—' }}</dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Department</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ auth()->user()->department?->name ?? '—' }}</dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Role</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ auth()->user()->getRoleNames()->implode(', ') ?: '—' }}</dd>
                </div>
                <div class="px-6 py-4">
                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider">Email</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800 truncate">{{ auth()->user()->email }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>
