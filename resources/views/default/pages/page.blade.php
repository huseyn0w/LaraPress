<?php
/**
 * Cmstack-Laravel
 * File: page.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 25.10.2019
 * Template Name: "Standart";
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */
?>


@extends(config('app.template_name').'/index')

@section('content')

<header class="border-b border-ink-100 bg-ink-50/60">
    <div class="mx-auto max-w-3xl px-5 py-20 text-center sm:px-8 sm:py-24">
        <h1 class="text-4xl font-medium tracking-tight text-ink-900 sm:text-5xl [text-wrap:balance]">{{$data->title}}</h1>
        @if(!empty($data->meta_description))
            <p class="mx-auto mt-5 max-w-2xl text-lg leading-relaxed text-ink-500">{{$data->meta_description}}</p>
        @endif
    </div>
</header>

<article class="mx-auto max-w-3xl px-5 py-16 sm:px-8 sm:py-20">
    @if(!empty($data->content))
        <div class="article-prose">
            {!! $data->content !!}
        </div>
    @else
        <h2 class="text-center text-2xl font-medium text-ink-500">@lang('default/page.no_content')</h2>
    @endif
</article>

@endsection
