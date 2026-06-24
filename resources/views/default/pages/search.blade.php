<?php
/**
 * Cmstack-Laravel
 * File: search.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 15.11.2019
 * Template Name: "Search Page";
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */

$current_lang = get_current_lang_prefix();

?>
@extends(config('app.template_name').'/index')

@section('content')
    <section class="border-b border-ink-100 bg-ink-50/60">
        <div class="mx-auto max-w-3xl px-5 py-16 sm:px-8 sm:py-20">
            <h1 class="mb-8 text-center text-3xl font-medium tracking-tight text-ink-900 sm:text-4xl">@lang('default/header.searchpage_title')</h1>

            <form action="{{route('get_search_result')}}" method="POST" class="space-y-5">
                @csrf
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ink-400">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="9" r="6"/><path d="m18 18-4-4" stroke-linecap="round"/></svg>
                    </span>
                    <input type="text" name="query" required
                           value="{{ isset($searchData['query']) ? $searchData['query'] : '' }}"
                           placeholder="@lang('default/page.search_placeholder')"
                           class="field-input !rounded-full !py-4 pl-12 pr-32 text-lg">
                    <button type="submit" class="btn-primary absolute right-1.5 top-1/2 -translate-y-1/2 !px-5 !py-2.5">
                        @lang('default/header.search')
                    </button>
                </div>

                <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                    <label for="filter" class="text-sm font-medium text-ink-600">@lang('default/page.filter_by')</label>
                    <select name="filter" id="filter"
                            class="rounded-full border-ink-200 bg-surface py-2 pl-4 pr-9 text-sm text-ink-800 shadow-sm transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                        <option value="post">@lang('default/page.filter_post')</option>
                        <option value="page">@lang('default/page.filter_page')</option>
                        <option value="user">@lang('default/page.filter_user')</option>
                        <option value="category">@lang('default/page.filter_category')</option>
                    </select>
                </div>

                @if(isset($searchData) && count($searchData) > 0)
                    @php
                        $results_word = $searchData['result']->total() > 1 ? trans('default/page.results') : trans('default/page.result');
                    @endphp
                    <p class="text-center text-sm text-ink-500">
                        {{$searchData['result']->total()}} {{$results_word}} @lang('default/page.found_for') &ldquo;{{$searchData['query']}}&rdquo;
                    </p>
                @endif

                {!! app('captcha')->render(); !!}
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-5 py-14 sm:px-8 sm:py-16">
        @if (count($errors) > 0)
            <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm text-brand-800" role="alert">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($searchData) && $searchData['result']->total() > 0)
            <h2 class="mb-8 text-2xl font-medium text-ink-900">
                @lang('default/page.search_result_headline'): &ldquo;{{$searchData['query']}}&rdquo;
            </h2>
            <ul class="divide-y divide-ink-100">
                @foreach($searchData['result'] as $item)
                    <li>
                        @php
                            if($searchData['type'] === "post") {
                                $result_url = env('APP_URL').'/'.$current_lang.'posts/'.$item->slug;
                                $result_label = $item->title;
                            } elseif($searchData['type'] === "page") {
                                $result_url = env('APP_URL').'/'.$item->slug;
                                $result_label = $item->title;
                            } elseif($searchData['type'] === "user") {
                                $result_url = env('APP_URL').$current_lang.'users/'.$item->username;
                                $result_label = $item->username;
                            } else {
                                $result_url = env('APP_URL').'/'.$current_lang.'category/'.$item->slug;
                                $result_label = $item->title;
                            }
                        @endphp
                        <a href="{{$result_url}}" class="group flex items-center justify-between gap-4 py-5 transition-colors">
                            <h3 class="font-serif text-xl font-medium text-ink-900 transition-colors group-hover:text-brand-700">{{$result_label}}</h3>
                            <svg class="h-5 w-5 shrink-0 text-ink-300 transition group-hover:translate-x-1 group-hover:text-brand-600" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 10h11M11 5l5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-10">
                @php
                    $links = $searchData['result']->links();
                    echo pretty_search_url($links, $searchData['type'], $searchData['query']);
                @endphp
            </div>
        @elseif(isset($searchData) && $searchData['result']->total() === 0)
            <div class="py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-ink-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                <h3 class="mt-5 text-xl font-medium text-ink-700">@lang('default/page.search_nothing_found')</h3>
            </div>
        @endif
    </section>
@endsection
