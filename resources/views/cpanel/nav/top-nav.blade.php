<?php
/**
 * LaraPress CMS
 * File: top-nav.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */

$languages = get_languages();
?>
<header class="sticky top-0 z-sticky flex h-16 items-center gap-3 border-b border-ink-200 bg-paper/90 px-4 backdrop-blur sm:px-6 lg:px-8">
    {{-- Mobile sidebar toggle (controls the Alpine state in core/index) --}}
    <button
        type="button"
        @click="sidebarOpen = true"
        class="-ml-1 inline-flex h-10 w-10 items-center justify-center rounded-lg text-ink-600 transition hover:bg-ink-100 lg:hidden"
        aria-label="Open navigation"
    >
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <p class="hidden text-sm text-ink-500 sm:block">
        @lang('cpanel/nav/top.logged_in_as')
        <span class="font-semibold text-ink-900">{{$current_user->username}}</span>
    </p>

    <div class="ml-auto flex items-center gap-1.5">
        <a
            href="{{route('front_pages',['slug' => '/'])}}"
            target="_blank"
            class="hidden items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-ink-600 transition hover:bg-ink-100 hover:text-ink-900 sm:inline-flex"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3h7v7M21 3l-9 9M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
            @lang('cpanel/nav/top.homepage')
        </a>

        {{-- Language switcher --}}
        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-ink-600 transition hover:bg-ink-100 hover:text-ink-900"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>
                <span class="hidden sm:inline">@lang('cpanel/nav/top.change_language')</span>
                <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
            <div
                x-cloak
                x-show="open"
                x-transition.origin.top.right
                class="absolute right-0 top-full z-dropdown mt-1.5 w-48 origin-top-right overflow-hidden rounded-xl border border-ink-200 bg-surface p-1.5 shadow-lift"
            >
                @foreach($languages as $code => $language)
                    <a href="{{route('lang_route', ['locale' => $code])}}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-ink-600 transition hover:bg-ink-50 hover:text-ink-900">
                        <img src="{{$language['icon']}}" alt="{{$language['title']}}" class="h-4 w-auto rounded-sm" />
                        <span>{{$language['title']}}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <span class="mx-1 hidden h-6 w-px bg-ink-200 sm:block"></span>

        <a
            href="{{ route('logout') }}"
            class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-ink-600 transition hover:bg-danger-50 hover:text-danger-600"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            <span class="hidden sm:inline">@lang('cpanel/nav/top.log_out')</span>
        </a>
    </div>
</header>
