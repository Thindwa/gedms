<aside class="app-sidebar"
       :style="{ width: sidebarWidth }"
       :class="(sidebarOpen ? 'translate-x-0' : '-translate-x-full') + ' lg:translate-x-0'">
    <div class="app-sidebar-inner">
        {{-- Logo --}}
        <div class="app-sidebar-logo" :class="{ 'justify-center px-3': sidebarCollapsed }">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0" :class="{ 'justify-center': sidebarCollapsed }">
                <div class="app-sidebar-logo-icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="app-sidebar-logo-text" x-show="!sidebarCollapsed" x-cloak>Malawi Govt EDMS</span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="app-sidebar-nav">
            <a href="{{ route('dashboard') }}" class="app-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Dashboard' : null">
                <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>Dashboard</span>
            </a>

            <a href="{{ route('files.index') }}" class="app-sidebar-link {{ request()->routeIs('files.*') ? 'active drive' : '' }}" :title="sidebarCollapsed ? 'Drive' : null">
                <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>Drive</span>
            </a>

            <a href="{{ route('documents.index') }}" class="app-sidebar-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'EDMS' : null">
                <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>EDMS</span>
            </a>

            <a href="{{ route('memos.index') }}" class="app-sidebar-link {{ request()->routeIs('memos.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Memos' : null">
                <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>Memos</span>
            </a>


            @php $isDeptAdmin = Auth::user()?->can('manage-department') && !Auth::user()?->can('manage-system') && Auth::user()?->department_id; @endphp
            @if (Auth::user()?->can('approve-documents') && !$isDeptAdmin)
            <a href="{{ route('approvals.index') }}" class="app-sidebar-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Pending Approvals' : null">
                <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span x-show="!sidebarCollapsed" x-cloak>Pending Approvals</span>
            </a>
            @endif

            @if (Auth::user()?->can('manage-document-types') || Auth::user()?->can('manage-sensitivity-levels') || Auth::user()?->can('manage-workflows') || Auth::user()?->can('manage-retention') || Auth::user()?->can('manage-roles') || Auth::user()?->can('view-audit-logs') || Auth::user()?->can('view-audit-only'))
            <div class="app-sidebar-section">
                <p class="app-sidebar-section-title" x-show="!sidebarCollapsed" x-cloak>Administration</p>
                @if ($isDeptAdmin)
                <a href="{{ route('admin.department.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.department.index') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Dashboard' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Dashboard</span>
                </a>
                @if (Auth::user()?->can('manage-users'))
                <a href="{{ route('admin.users.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Users' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Users</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-department'))
                <a href="{{ route('admin.sections.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.sections*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Sections' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Sections</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-roles'))
                <a href="{{ route('admin.roles.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Roles & Permissions' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Roles &amp; Permissions</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-workflows'))
                <a href="{{ route('admin.workflows.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.workflows*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Workflow Settings' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Workflow Settings</span>
                </a>
                @endif
                @if (Auth::user()?->can('view-audit-logs'))
                <a href="{{ route('admin.reports.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Reports' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Reports</span>
                </a>
                @endif
                @if (Auth::user()?->can('view-audit-logs') || Auth::user()?->can('view-audit-only'))
                <a href="{{ route('admin.audit-logs.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Audit Logs' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Audit Logs</span>
                </a>
                @endif
                <a href="{{ route('admin.department.settings') }}" class="app-sidebar-link {{ request()->routeIs('admin.department.settings') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Settings' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Settings</span>
                </a>
                @else
                <a href="{{ Auth::user()?->can('manage-system') ? route('admin.index') : route('admin.department.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.index') || (request()->routeIs('admin.department*') && !Auth::user()?->can('manage-system')) ? 'active' : '' }}" :title="sidebarCollapsed ? 'Admin' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Admin</span>
                </a>
                @if (Auth::user()?->can('manage-roles'))
                <a href="{{ route('admin.roles.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Roles & Permissions' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Roles & Permissions</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-document-types'))
                <a href="{{ route('admin.document-types.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.document-types.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Document Types' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Document Types</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-sensitivity-levels'))
                <a href="{{ route('admin.sensitivity-levels.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.sensitivity-levels.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Sensitivity Levels' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Sensitivity Levels</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-workflows'))
                <a href="{{ route('admin.workflows.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.workflows.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Workflows' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Workflows</span>
                </a>
                @endif
                @if (Auth::user()?->can('manage-retention-disposition') || Auth::user()?->can('manage-retention'))
                <a href="{{ route('admin.retention-rules.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.retention-rules.*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Retention Rules' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Retention Rules</span>
                </a>
                @endif
                @if (Auth::user()?->can('view-audit-logs') || Auth::user()?->can('view-audit-only'))
                <a href="{{ route('admin.audit-logs.index') }}" class="app-sidebar-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}" :title="sidebarCollapsed ? 'Audit Logs' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!sidebarCollapsed" x-cloak>Audit Logs</span>
                </a>
                @endif
                @endif
            </div>
            @endif
        </nav>

        {{-- Footer --}}
        <div class="app-sidebar-footer">
            <p class="app-sidebar-footer-text" title="{{ Auth::user()->email }}" x-show="!sidebarCollapsed" x-cloak>{{ Auth::user()->department?->name ?? '—' }}</p>
            <p class="app-sidebar-footer-sub" x-show="!sidebarCollapsed" x-cloak>{{ Auth::user()->ministry?->name ?? '—' }}</p>
            <button @click="sidebarCollapsed = !sidebarCollapsed" type="button" class="app-sidebar-collapse-btn" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" aria-label="Toggle sidebar">
                <svg class="w-5 h-5 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>
    </div>
</aside>
