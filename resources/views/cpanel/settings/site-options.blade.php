<?php
/**
 * LaraPress CMS
 * File: site-options.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    @php
        $logo_url = $site_options->logo_url;
        $copyright = $site_options->copyright;
        $github_url = $site_options->github_url;
        $linkedin_url = $site_options->linkedin_url;
    @endphp

    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/settings.site_options_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $update_message ? __('cpanel/settings.site_options_updates_success') : $update_message }}</strong>
            </div>
        @endif

        <form action="{{ route('cpanel_update_site_options') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body space-y-1">
                    <div class="field">
                        <label for="custom_input_image" class="field-label">@lang('cpanel/settings.logo')</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <span class="input-group-btn">
                                <a id="lfm" data-input="thumbnail" data-preview="holder" class="choose-image">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path d="m21 16-5-5L5 19"/></svg>
                                    @lang('cpanel/settings.choose_image')
                                </a>
                            </span>
                            <input id="thumbnail" class="form-control" type="text" name="logo_url" value="{{ old('logo_url', $logo_url) }}">
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.footer_copyright')</label>
                        <input type="text" required name="copyright" class="form-control" value="{{ old('copyright', $copyright) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.linkedin_url')</label>
                        <input type="text" required name="linkedin_url" class="form-control" value="{{ old('linkedin_url', $linkedin_url) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.github_url')</label>
                        <input type="text" required name="github_url" class="form-control" value="{{ old('github_url', $github_url) }}">
                    </div>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/settings.update_button_label')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('finalscripts')
    <script>
        var site_url = "<?php echo env('APP_URL'); ?>/";
    </script>
    <script src="{{asset('')}}/vendor/laravel-filemanager/js/lfm.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-image.js"></script>
@endpush
