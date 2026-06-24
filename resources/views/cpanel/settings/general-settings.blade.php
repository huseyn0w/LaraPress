<?php
/**
 * Cmstack-Laravel
 * File: general-settings.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    @php
        $website_name = $general_settings->website_name;
        $tagline = $general_settings->tagline;
        $email = $general_settings->contact_email;
        $membership = $general_settings->membership;
        $active_template_name = $general_settings->active_template_name;
        $posts_per_page = $general_settings->posts_per_page;
        $comments_per_page = $general_settings->comments_per_page;
        $directories = get_front_templates_array();
    @endphp

    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/settings.general_settings_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $update_message ? __('cpanel/settings.general_settings_updates_success') : $update_message }}</strong>
            </div>
        @endif

        <form action="{{ route('cpanel_update_general_settings') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body space-y-1">
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.website_name')</label>
                        <input type="text" required name="website_name" class="form-control" value="{{ old('website_name', $website_name) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.tagline')</label>
                        <div class="field-desc mb-1.5"><p class="mt-0">@lang('cpanel/settings.tagline_content')</p></div>
                        <textarea rows="3" required name="tagline" class="form-control">{{ old('tagline', $tagline) }}</textarea>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.contact_email')</label>
                        <input type="email" required name="contact_email" class="form-control" value="{{ old('contact_email', $email) }}">
                    </div>
                    <div class="field">
                        <label for="membership" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                            <input class="form-check-input" id="membership" name="membership" type="checkbox" {{$membership == 1 ? 'checked value=1' : null}}>
                            @lang('cpanel/settings.membership')
                        </label>
                    </div>
                    <div class="field">
                        <label for="inputState" class="field-label">@lang('cpanel/settings.active_template')</label>
                        <select id="inputState" name="active_template_name" required class="form-control">
                            @forelse($directories as $key => $value)
                                <option value="{{$value}}" {{ $value === $active_template_name ? 'selected' : '' }}>{{$value}}</option>
                            @empty
                                <option disabled>@lang('cpanel/settings.no_template')</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="grid grid-cols-1 gap-x-5 sm:grid-cols-2">
                        <div class="field">
                            <label class="field-label">@lang('cpanel/settings.posts_per_page')</label>
                            <input type="number" min="1" required name="posts_per_page" class="form-control" value="{{ old('posts_per_page', $posts_per_page) }}">
                        </div>
                        <div class="field">
                            <label class="field-label">@lang('cpanel/settings.comments_per_page')</label>
                            <input type="number" min="1" required name="comments_per_page" class="form-control" value="{{ old('comments_per_page', $comments_per_page) }}">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/settings.update_button_label')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
