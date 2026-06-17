<?php
/**
 * LaraPress CMS
 * File: home.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */
?>
@extends('cpanel.core.index')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/home.dashboard')</h1>
        <p class="mt-1 text-sm text-ink-500">@lang('cpanel/home.greetings')</p>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Latest posts --}}
        <section class="rounded-xl border border-ink-200/70 bg-surface shadow-card">
            <header class="flex items-center gap-2.5 border-b border-ink-100 px-5 py-4">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M5 3h14a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm2 4v2h10V7H7Zm0 4v2h10v-2H7Zm0 4v2h6v-2H7Z"/></svg>
                </span>
                <h2 class="text-sm font-semibold text-ink-900">@lang('cpanel/home.last_posts')</h2>
            </header>
            <ul class="divide-y divide-ink-100">
                @forelse($posts as $post)
                    <li class="px-5 py-3 text-sm text-ink-700">
                        <a href="{{route('cpanel_edit_post', ['id' => $post->id, 'lang' => get_current_lang()])}}" class="line-clamp-1 transition-colors hover:text-brand-700">{{$post->title}}</a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-400">@lang('cpanel/home.no_posts')</li>
                @endforelse
            </ul>
        </section>

        {{-- Latest comments --}}
        <section class="rounded-xl border border-ink-200/70 bg-surface shadow-card">
            <header class="flex items-center gap-2.5 border-b border-ink-100 px-5 py-4">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-info-50 text-info-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H9l-5 4V5a1 1 0 0 1 1-1Z"/></svg>
                </span>
                <h2 class="text-sm font-semibold text-ink-900">@lang('cpanel/home.last_comments')</h2>
            </header>
            <ul class="divide-y divide-ink-100">
                @forelse($comments as $comment)
                    <li class="px-5 py-3 text-sm text-ink-600">
                        <p class="line-clamp-2">{{$comment->comment}}</p>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-400">@lang('cpanel/home.no_comments')</li>
                @endforelse
            </ul>
        </section>

        {{-- Latest users --}}
        <section class="rounded-xl border border-ink-200/70 bg-surface shadow-card">
            <header class="flex items-center gap-2.5 border-b border-ink-100 px-5 py-4">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-success-50 text-success-600">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z"/></svg>
                </span>
                <h2 class="text-sm font-semibold text-ink-900">@lang('cpanel/home.last_users')</h2>
            </header>
            <ul class="divide-y divide-ink-100">
                @forelse($users as $user)
                    <li class="flex items-center gap-3 px-5 py-3 text-sm text-ink-700">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-ink-100 text-xs font-semibold uppercase text-ink-600">{{ mb_substr($user->username, 0, 1) }}</span>
                        <span class="line-clamp-1">{{$user->username}}</span>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-ink-400">@lang('cpanel/home.no_users')</li>
                @endforelse
            </ul>
        </section>
    </div>
</div>
@endsection
