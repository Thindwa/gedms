<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Malawi Government EDMS @isset($title) — {{ $title }} @endisset</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="app-body">
    <div x-data="appLayout" class="app-wrapper">
        {{-- Sidebar (width controlled by inline style for reliable collapse) --}}
        @include('layouts.sidebar')

        {{-- Main area --}}
        <div class="app-main">
            {{-- Header --}}
            <header class="app-header">
                <div class="app-header-inner">
                    <div class="app-header-left">
                        <button @click="sidebarOpen = !sidebarOpen" type="button" class="app-mobile-menu-btn" aria-label="Toggle menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        @php $pageHeader = $header ?? view()->yieldContent('header'); @endphp
                        @if($pageHeader)
                            <h1 class="app-page-title">{{ $pageHeader }}</h1>
                        @endif
                        <div class="app-context-badge">
                            <span>{{ Auth::user()->ministry?->name ?? '—' }}</span>
                            @if(Auth::user()->department_id)
                                <span class="text-slate-400">/</span>
                                <span>{{ Auth::user()->department?->name ?? '—' }}</span>
                            @endif
                            @if(Auth::user()->section_id)
                                <span class="text-slate-400">/</span>
                                <span>{{ Auth::user()->section?->name ?? '—' }}</span>
                            @endif
                        </div>
                        @if(request()->routeIs('files.*') || request()->routeIs('documents.*') || request()->routeIs('search.index') || request()->routeIs('admin.*'))
                        <form action="{{ route('search.index') }}" method="GET" class="app-search-form">
                            <svg class="app-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="search" name="q" placeholder="Search" class="app-search-input">
                        </form>
                        @endif
                    </div>

                    <div class="app-header-right">
                        <button type="button" class="app-icon-btn" title="Notifications" aria-label="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </button>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false" type="button" class="app-user-btn">
                                <span class="app-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                                <span class="app-user-info">
                                    <span class="app-user-name">{{ Auth::user()->name }}</span>
                                    <span class="app-user-role">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                                </span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition class="app-dropdown">
                                <a href="{{ route('profile.edit') }}" class="app-dropdown-item">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="app-dropdown-item w-full text-left">Log out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="app-content">
                {!! $slot ?? view()->yieldContent('content') !!}
            </main>
        </div>

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="app-overlay"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         aria-hidden="true">
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('appLayout', () => ({
                sidebarOpen: false,
                sidebarCollapsed: false,
                get sidebarWidth() {
                    return this.sidebarCollapsed ? '4rem' : '16rem';
                }
            }));
        });
    </script>
</body>
</html>
