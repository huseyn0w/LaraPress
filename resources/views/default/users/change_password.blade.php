<?php
/**
 * Laravella CMS
 * File: change_password.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 12.11.2019
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */
?>

@extends(config('app.template_name').'/index')

@section('content')

    @php
        $home_page_data = get_data(1, 'page', ['slug', 'title']);
    @endphp

    @include(config('app.template_name').'.partials.banner', [
        'title'  => __('default/change_password.headline'),
        'crumbs' => [
            ['label' => $home_page_data->title, 'url' => env('APP_URL')],
            ['label' => __('default/change_password.edit_profile'), 'url' => route('get_user_info')],
            ['label' => __('default/change_password.change_password'), 'url' => null],
        ],
    ])

    <section class="mx-auto max-w-xl px-5 py-16 sm:px-8 sm:py-20">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm text-brand-800" role="alert">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (Session::has('message'))
            @if (Session::get('message'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">@lang('default/change_password.password_updated')</div>
            @else
                <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm font-medium text-brand-800" role="alert">@lang('default/change_password.problem_occurred')</div>
            @endif
        @endif

        <form action="{{ route('change_password_action') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @method('PUT')
            @csrf

            <div>
                <label for="current_password" class="field-label">@lang('default/change_password.current_password')</label>
                <input type="password" required id="current_password" name="current_password" class="field-input" autocomplete="current-password">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="field-label">@lang('default/change_password.new_password')</label>
                    <input type="password" required id="password" name="password" class="field-input" autocomplete="new-password">
                </div>
                <div>
                    <label for="confirm_password" class="field-label">@lang('default/change_password.confirm_new_password')</label>
                    <input type="password" required id="confirm_password" name="password_confirmation" class="field-input" autocomplete="new-password">
                </div>
            </div>

            <div>
                {!! app('captcha')->render(); !!}
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary">@lang('default/change_password.change_password')</button>
            </div>
        </form>
    </section>

@endsection
