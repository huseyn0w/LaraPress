<?php
/**
 * LaraPress CMS
 * File: new_page.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 16.08.2019
 */
?>

@extends('cpanel.core.index')

@push('extrastyles')
    <link rel="stylesheet" href="{{asset('admin')}}/css/datepicker.min.css">
@endpush

@php
    $form_action = route('cpanel_save_new_page');
    if(!empty(request()->route('id')))  $form_action = route('cpanel_save_new_page', ['id' => request()->route('id')]);
@endphp

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/pages.new_page_headline')</h1>
        </div>

        @include('cpanel.core.flash')

        <form action="{{$form_action}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                                <div class="field">
                                    <label for="cpanel_title" class="field-label">@lang('cpanel/pages.title')</label>
                                    <input type="text" id="cpanel_title" required class="form-control" name="title" value="{{ old('title') }}">
                                </div>
                                <div class="field">
                                    <label for="cpanel_slug" class="field-label">@lang('cpanel/pages.slug')</label>
                                    <input type="text" id="cpanel_slug" required class="form-control" name="slug" value="{{ old('slug') }}">
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.content')</label>
                                <textarea name="content" id="editor" class="my-editor form-control">{{old('content')}}</textarea>
                            </div>
                            @include('cpanel.core.seo')
                            @include('cpanel.core.custom-fields')
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="card">
                        <div class="card-body">
                            @include('cpanel.core.translation')
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.author')</label>
                                <select name="author_id" id="author_id" class="form-control">
                                    @foreach($users_list as $user)
                                        <option value="{{$user->id}}" {{$user->username === Auth::user()->username ? 'selected' : ''}}>{{$user->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.publish_date')</label>
                                <input class="form-control" autocomplete="off" name="created_at" value="{{ \Carbon\Carbon::now() }}" required id="date_time_picker" type="text" />
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.status')</label>
                                <select name="status" id="user_role" class="form-control">
                                    <option value="0">@lang('cpanel/pages.status_private')</option>
                                    <option value="1" selected>@lang('cpanel/pages.status_published')</option>
                                </select>
                            </div>
                            @if(!empty($page_templates) && $page_templates)
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/pages.page_template')</label>
                                    <select name="template" class="form-control">
                                        @foreach($page_templates as $file_name => $template_header)
                                            <option value="{{$file_name}}" {{$file_name === 'page' ? 'selected' : null}}>{{$template_header}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                            <button type="submit" class="btn btn-info">@lang('cpanel/pages.publish_button_label')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @include('cpanel.core.modals')
@endsection

@push('extrascripts')
    <script src="https://cdn.tiny.cloud/1/4vyoa49f4irghhao6v5lpc7z5z2hvhgau8wsjj1y9g65ovse/tinymce/4/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{asset('admin')}}/js/datepicker.min.js"></script>
    <script src="{{asset('admin')}}/js/i18n/datepicker.en.js"></script>
    <script src="{{asset('')}}/vendor/laravel-filemanager/js/lfm.js"></script>
@endpush
@push('finalscripts')
    @include('cpanel.core.custom-fields-variables')
    <script src="{{asset('admin')}}/js/page.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-text.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-textarea.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-image.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-link.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-category.js"></script>
    <script src="{{asset('admin')}}/js/custom-fields/custom-repeater.js"></script>
@endpush
