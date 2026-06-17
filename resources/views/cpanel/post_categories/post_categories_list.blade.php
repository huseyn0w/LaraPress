<?php
/**
 * LaraPress CMS
 * File: post_categories_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 31.08.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/categories.list_headline')</h1>
            <a href="{{route('cpanel_add_new_category')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/categories.add_new_category')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/categories.bulky_deleted_message') : __('cpanel/categories.bulky_deleted_error_message') }}</strong></div>
        @endif
        @if (Session::get('category_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/categories.category_added')</strong></div>
        @endif

        <div class="card overflow-hidden">
            <form method="POST" action="{{route('cpanel_category_bulk_delete')}}">
                @csrf
                @method('DELETE')
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="select-cover mb-0">
                        <select id="inputState" name="categories_action" class="form-control">
                            <option selected="selected">@lang('cpanel/categories.bulk_action_label')</option>
                            <option value="delete">@lang('cpanel/categories.bulk_action_delete_label')</option>
                        </select>
                        <button type="submit" class="btn btn-ghost">@lang('cpanel/categories.bulk_action_apply')</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table users-table">
                        <thead>
                            <tr>
                                <th class="w-10"><input class="form-check-input" id="selectAll" name="allcategories" type="checkbox" aria-label="Select all"></th>
                                <th class="w-12">№</th>
                                <th>@lang('cpanel/categories.table_name')</th>
                                <th>@lang('cpanel/categories.table_action')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($category_count = 0)
                        @forelse($categories_list as $category)
                            @php($category_count++)
                            <tr>
                                <td>
                                    @if($category->id !== 1)
                                        <input class="form-check-input categories-checkbox-input" id="category_{{$category->id}}" name="categories[]" type="checkbox" value="{{$category->id}}" aria-label="Select category">
                                    @endif
                                </td>
                                <td class="text-ink-400">{{$category_count}}</td>
                                <td class="font-medium text-ink-900">{{$category->title}}</td>
                                <td>
                                    <span class="user_actions">
                                        <a href="{{route('cpanel_edit_category', ['id' => $category->id, 'lang' => get_current_lang()])}}" target="_blank">@lang('cpanel/categories.edit_category')</a>
                                        @if($category->id !== 1)
                                            <input type="hidden" class="deleted_category_id" value="{{$category->id}}" name="deleted_category_id">
                                            <button type="button" class="delete_category">@lang('cpanel/categories.delete_category')</button>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-ink-400">@lang('cpanel/categories.not_found')</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $categories_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/categories.js_delete_confirmation')',
            delete_success = '@lang('cpanel/categories.js_delete_success')',
            error_message = '@lang('cpanel/categories.js_error')';
    </script>
    <script src="{{asset('admin')}}/js/category.js"></script>
@endpush
