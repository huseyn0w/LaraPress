<?php
/**
 * Laravella CMS
 * File: edit_page.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 16.08.2019
 */
?>

@extends('cpanel.core.index')

@push('extrastyles')
    <link rel="stylesheet" href="{{asset('admin')}}/css/datepicker.min.css">
@endpush

@section('content')
    @php
        $page_slug = $entity->slug === "/" ? '' : $entity->slug;
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/pages.edit_page_headline')</h1>
            <p class="mt-1 text-sm text-ink-500">
                @lang('cpanel/pages.url_preview')
                <a href="{{env('APP_URL')}}/{{ old('slug',$page_slug) }}" class="font-medium text-brand-700 hover:text-brand-800">{{env('APP_URL')}}/{{ old('slug',$page_slug) }}</a>
            </p>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/pages.updated_success') : __('cpanel/pages.updated_error') }}</strong></div>
        @endif

        <form action="{{ route('cpanel_update_page', ['id' => $entity->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method("PUT")
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                                <div class="field">
                                    <label for="cpanel_title" class="field-label">@lang('cpanel/pages.title')</label>
                                    <input type="text" id="cpanel_title" required class="form-control" name="title" value="{{ old('title', $entity->title) }}">
                                </div>
                                <div class="field">
                                    <label for="cpanel_slug" class="field-label">@lang('cpanel/pages.slug')</label>
                                    <input type="text" id="cpanel_slug" required class="form-control" name="slug" value="{{ old('slug',$entity->slug) }}">
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.content')</label>
                                <textarea name="content" id="editor" class="my-editor form-control">{{old('content',$entity->content)}}</textarea>
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
                                        <option value="{{$user->id}}" {{$user->id === $entity->author_id ? 'selected' : ''}}>{{$user->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.publish_date')</label>
                                <input class="form-control" value="{{old('updated_at', $entity->updated_at)}}" autocomplete="off" name="updated_at" required id="date_time_picker" type="text" />
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/pages.status')</label>
                                <select name="status" id="user_role" class="form-control">
                                    <option value="0" {{$entity->status === 0 ? 'selected' :null}}>@lang('cpanel/pages.status_private')</option>
                                    <option value="1" {{$entity->status === 1 ? 'selected' :null}}>@lang('cpanel/pages.status_published')</option>
                                </select>
                            </div>
                            @if(!empty($page_templates) && $page_templates)
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/pages.page_template')</label>
                                    <select name="template" class="form-control">
                                        @foreach($page_templates as $file_name => $template_header)
                                            <option value="{{$file_name}}" {{$entity->template === $file_name ? 'selected' : null}}>{{$template_header}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                            <button type="submit" class="btn btn-info">@lang('cpanel/pages.update_button_label')</button>
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
