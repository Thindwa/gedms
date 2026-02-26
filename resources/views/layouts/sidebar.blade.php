@php
    $user       = Auth::user();
    $icons      = config('navigation.icons', []);
    $isDeptAdmin = $user?->can('manage-department') && !$user?->can('manage-system') && $user?->department_id;

    // Resolve whether a user can see an item
    $canSee = function (array $item) use ($user, $isDeptAdmin) {
        if (!empty($item['hide_for_dept_admin']) && $isDeptAdmin) return false;
        if (empty($item['permission'])) return true;
        foreach ((array) $item['permission'] as $p) {
            if ($user?->can($p)) return true;
        }
        return false;
    };

    // Resolve the href for an item (supports route_requires / route_fallback)
    $resolveHref = function (array $item) use ($user) {
        if (!empty($item['route_requires']) && !$user?->can($item['route_requires'])) {
            return route($item['route_fallback'] ?? $item['route']);
        }
        return route($item['route']);
    };

    // Check active state (string or array of patterns)
    $isActive = function (array $item) {
        foreach ((array) ($item['active'] ?? $item['route']) as $pattern) {
            if (request()->routeIs($pattern)) return true;
        }
        return false;
    };

    // Determine which admin items to show
    $adminPerms = \Spatie\Permission\Models\Permission::where('guard_name', 'web')
        ->where(fn ($q) => $q->where('name', 'like', 'manage-%')->orWhere('name', 'like', 'view-audit%'))
        ->pluck('name');
    $hasAdminAccess = $adminPerms->contains(fn ($p) => $user?->can($p));
    $adminItems     = $isDeptAdmin ? config('navigation.admin_dept', []) : config('navigation.admin_system', []);
@endphp

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
            {{-- Main navigation (config-driven) --}}
            @foreach (config('navigation.main', []) as $item)
                @if ($canSee($item))
                <a href="{{ $resolveHref($item) }}"
                   class="app-sidebar-link {{ $isActive($item) ? 'active' . (!empty($item['active_class']) ? ' '.$item['active_class'] : '') : '' }}"
                   :title="sidebarCollapsed ? '{{ $item['label'] }}' : null">
                    <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @foreach ($icons[$item['icon']] ?? [] as $d)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $d }}"/>
                        @endforeach
                    </svg>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                </a>
                @endif
            @endforeach

            {{-- Administration section (config-driven) --}}
            @if ($hasAdminAccess)
            <div class="app-sidebar-section">
                <p class="app-sidebar-section-title" x-show="!sidebarCollapsed" x-cloak>Administration</p>

                @foreach ($adminItems as $item)
                    @if ($canSee($item))
                    <a href="{{ $resolveHref($item) }}"
                       class="app-sidebar-link {{ $isActive($item) ? 'active' : '' }}"
                       :title="sidebarCollapsed ? '{{ $item['label'] }}' : null">
                        <svg class="app-sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @foreach ($icons[$item['icon']] ?? [] as $d)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $d }}"/>
                            @endforeach
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                    </a>
                    @endif
                @endforeach
            </div>
            @endif
        </nav>

        {{-- Footer --}}
        <div class="app-sidebar-footer">
            <p class="app-sidebar-footer-text" title="{{ $user->email }}" x-show="!sidebarCollapsed" x-cloak>{{ $user->department?->name ?? '—' }}</p>
            <p class="app-sidebar-footer-sub" x-show="!sidebarCollapsed" x-cloak>{{ $user->ministry?->name ?? '—' }}</p>
            <button @click="sidebarCollapsed = !sidebarCollapsed" type="button" class="app-sidebar-collapse-btn" :title="sidebarCollapsed ? 'Expand sidebar' : 'Collapse sidebar'" aria-label="Toggle sidebar">
                <svg class="w-5 h-5 transition-transform duration-200" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>
    </div>
</aside>
