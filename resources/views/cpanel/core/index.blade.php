<?php
/**
 * Laravella CMS
 * File: index.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 19.07.2019
 */
?>
@include('cpanel.core.header')
<body class="theme-admin">
<div
    class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]"
    x-data="{ sidebarOpen: false }"
    @keydown.escape.window="sidebarOpen = false"
>
    {{-- Mobile backdrop --}}
    <div
        x-cloak
        x-show="sidebarOpen"
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-backdrop bg-ink-950/40 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- Sidebar: off-canvas drawer below lg, static rail at lg+. --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-sidebar w-64 transform overflow-y-auto border-r border-ink-800/60 bg-ink-900 transition-transform duration-300 ease-out-expo lg:static lg:transform-none"
    >
        @include('cpanel.nav.left-nav')
    </aside>

    {{-- Main column --}}
    <div class="flex min-h-screen flex-col">
        @include('cpanel.nav.top-nav')
        <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
            @yield('content')
        </main>
        @include('cpanel.core.footer')
    </div>
</div>
