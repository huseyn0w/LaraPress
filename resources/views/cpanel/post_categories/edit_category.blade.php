<?php
/**
 * LaraPress CMS
 * File: edit_category.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 31.08.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/categories.edit_category_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/categories.updated_success') : __('cpanel/categories.updated_error') }}</strong></div>
        @endif

        <form action="{{ route('cpanel_update_category', ['id' => $entity->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    @include('cpanel.core.translation')

                    <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                        <div class="field">
                            <label for="cpanel_title" class="field-label">@lang('cpanel/categories.title')</label>
                            <input type="text" id="cpanel_title" required class="form-control" name="title" value="{{ old('title', $entity->title) }}">
                            <div class="field-desc"><p>@lang('cpanel/categories.title_desc')</p></div>
                        </div>
                        <div class="field">
                            <label for="cpanel_slug" class="field-label">@lang('cpanel/categories.slug')</label>
                            <input type="text" id="cpanel_slug" required class="form-control" name="slug" value="{{ old('slug', $entity->slug) }}">
                            <div class="field-desc"><p>@lang('cpanel/categories.slug_desc')</p></div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">@lang('cpanel/categories.parent_category')</label>
                        <select name="parent_category" class="form-control">
                            <option value="">@lang('cpanel/categories.no_parent_category')</option>
                            @foreach($categories_list as $category_item)
                                @if($category_item->id === $entity->id) @continue @endif
                                <option value="{{$category_item->id}}" {{$category_item->id === $entity->parent_category ? 'selected': null}}>{{$category_item->title}}</option>
                            @endforeach
                        </select>
                        <div class="field-desc"><p>@lang('cpanel/categories.parent_category_desc')</p></div>
                    </div>

                    <div class="field">
                        <label class="field-label">@lang('cpanel/categories.description')</label>
                        <textarea name="description" class="form-control">{{ old('description', $entity->description) }}</textarea>
                        <div class="field-desc"><p>@lang('cpanel/categories.description_content')</p></div>
                    </div>

                    @include('cpanel.core.seo')
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/categories.update_button_label')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
