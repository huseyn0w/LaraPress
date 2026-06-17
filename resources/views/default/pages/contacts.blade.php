<?php
/**
 * Laravella CMS
 * File: contacts.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 08.10.2019
 * Template Name: "Contact Page";
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */
?>


@php
    if(is_logged_in()):
        $firstname = \Auth::user()->name;
        $lastname = \Auth::user()->surname;
        $email = \Auth::user()->email;
    endif;
@endphp

@extends(config('app.template_name').'/index')

@section('content')

@include(config('app.template_name').'.partials.banner', [
    'title'  => $data->title,
    'crumbs' => [
        ['label' => $home_page_data->title, 'url' => env('APP_URL')],
        ['label' => $data->title, 'url' => null],
    ],
])

<section class="mx-auto max-w-2xl px-5 py-16 sm:px-8 sm:py-20">
    @if (count($errors) > 0)
        <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm text-brand-800" role="alert">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="mb-6 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10" cy="10" r="8"/><path d="m6.5 10 2.5 2.5 4.5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ $message }}
        </div>
    @endif

    <div class="mb-8">
        <h2 class="font-serif text-2xl font-medium text-ink-900">@lang('default/page.have_question')</h2>
    </div>

    <form action="{{route('sendform')}}" method="post" class="space-y-5">
        @csrf
        @if(is_logged_in())
            <input type="hidden" name="first_name" value="{{old('first_name', $firstname)}}">
            <input type="hidden" name="last_name" value="{{old('last_name',$lastname)}}">
            <input type="hidden" name="email" value="{{old('email',$email)}}">
            <div>
                <label for="subject" class="field-label">@lang('default/page.subject')</label>
                <input id="subject" type="text" name="subject" placeholder="@lang('default/page.subject')" required class="field-input" value="{{old('subject')}}">
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="field-label">@lang('default/page.first_name')</label>
                    <input id="first_name" type="text" name="first_name" placeholder="@lang('default/page.first_name')" required class="field-input" value="{{old('first_name')}}">
                </div>
                <div>
                    <label for="last_name" class="field-label">@lang('default/page.last_name')</label>
                    <input id="last_name" type="text" name="last_name" placeholder="@lang('default/page.last_name')" required class="field-input" value="{{old('last_name')}}">
                </div>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="email" class="field-label">@lang('default/page.email')</label>
                    <input id="email" type="email" name="email" placeholder="@lang('default/page.email')" required class="field-input" value="{{old('email')}}">
                </div>
                <div>
                    <label for="subject" class="field-label">@lang('default/page.subject')</label>
                    <input id="subject" type="text" name="subject" placeholder="@lang('default/page.subject')" required class="field-input" value="{{old('subject')}}">
                </div>
            </div>
        @endif

        <div>
            <label for="message" class="field-label">@lang('default/page.message')</label>
            <textarea id="message" name="message" rows="6" placeholder="@lang('default/page.message')" required class="field-input resize-y">{{old('message')}}</textarea>
        </div>

        {{-- Captcha (renders nothing when keys are absent; keeps g-recaptcha-response). --}}
        <div class="[&_div]:!mt-0">
            {!! app('captcha')->render(); !!}
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary">
                @lang('default/page.submit')
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10h11M11 5l5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </form>
</section>

@endsection
