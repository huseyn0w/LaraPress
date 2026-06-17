<?php
/**
 * LaraPress CMS
 * File: seo-settings.blade.php
 * Phase 7 (SEO/GEO): global SEO settings admin page.
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/settings.seo_settings_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $update_message ? __('cpanel/settings.seo_settings_updates_success') : $update_message }}</strong>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cpanel_update_seo_settings') }}" method="POST">
            @csrf

            {{-- Meta defaults --}}
            <div class="card">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.seo_meta_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_title_separator')</label>
                        <input type="text" required maxlength="8" name="title_separator" class="form-control" value="{{ old('title_separator', $seo_settings->title_separator) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_default_description')</label>
                        <textarea rows="3" name="default_meta_description" class="form-control">{{ old('default_meta_description', $seo_settings->default_meta_description) }}</textarea>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_default_og_image')</label>
                        <input type="text" name="default_og_image" class="form-control" value="{{ old('default_og_image', $seo_settings->default_og_image) }}" placeholder="https://...">
                    </div>
                </div>
            </div>

            {{-- Social --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.seo_social_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_og_site_name')</label>
                        <input type="text" name="og_site_name" class="form-control" value="{{ old('og_site_name', $seo_settings->og_site_name) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_twitter_handle')</label>
                        <input type="text" name="twitter_handle" class="form-control" value="{{ old('twitter_handle', $seo_settings->twitter_handle) }}" placeholder="@yourhandle">
                    </div>
                </div>
            </div>

            {{-- Verification --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.seo_verification_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_google_verification')</label>
                        <input type="text" name="google_site_verification" class="form-control" value="{{ old('google_site_verification', $seo_settings->google_site_verification) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_bing_verification')</label>
                        <input type="text" name="bing_site_verification" class="form-control" value="{{ old('bing_site_verification', $seo_settings->bing_site_verification) }}">
                    </div>
                </div>
            </div>

            {{-- Analytics --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.seo_analytics_section')</h2>
                    <div class="field-desc mb-2"><p class="mt-0">@lang('cpanel/settings.seo_analytics_help')</p></div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_ga4_id')</label>
                        <input type="text" name="ga4_measurement_id" class="form-control" value="{{ old('ga4_measurement_id', $seo_settings->ga4_measurement_id) }}" placeholder="G-XXXXXXX">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_gtm_id')</label>
                        <input type="text" name="gtm_container_id" class="form-control" value="{{ old('gtm_container_id', $seo_settings->gtm_container_id) }}" placeholder="GTM-XXXXXXX">
                    </div>
                </div>
            </div>

            {{-- Indexing / robots / sitemap --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.seo_indexing_section')</h2>
                    <div class="field">
                        <label for="discourage" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                            <input class="form-check-input" id="discourage" name="discourage_search_engines" type="checkbox" value="1" {{ old('discourage_search_engines', $seo_settings->discourage_search_engines) ? 'checked' : '' }}>
                            @lang('cpanel/settings.seo_discourage')
                        </label>
                        <div class="field-desc"><p>@lang('cpanel/settings.seo_discourage_help')</p></div>
                    </div>
                    <div class="field">
                        <label for="sitemap_enabled" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                            <input class="form-check-input" id="sitemap_enabled" name="sitemap_enabled" type="checkbox" value="1" {{ old('sitemap_enabled', $seo_settings->sitemap_enabled) ? 'checked' : '' }}>
                            @lang('cpanel/settings.seo_sitemap_enabled')
                        </label>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.seo_robots_extra')</label>
                        <textarea rows="4" name="robots_extra" class="form-control font-mono text-sm">{{ old('robots_extra', $seo_settings->robots_extra) }}</textarea>
                        <div class="field-desc"><p>@lang('cpanel/settings.seo_robots_extra_help')</p></div>
                    </div>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/settings.update_button_label')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
