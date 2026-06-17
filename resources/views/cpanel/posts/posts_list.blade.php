<?php
/**
 * Laravella CMS
 * File: posts_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 01.09.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@php
$route_name = Route::current()->getName();
$is_trash = $route_name == "cpanel_trashed_posts_list";
@endphp

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/posts.general_posts')</h1>
            <a href="{{route('cpanel_add_new_post')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/posts.add_new_post')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if (Session::get('post_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/posts.post_added')</strong></div>
        @endif
        @if (($update_message = Session::get('deleted')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/posts.bulky_deleted_message') : __('cpanel/posts.bulky_error_message') }}</strong></div>
        @endif
        @if (($update_message = Session::get('restored')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/posts.bulky_restored_message') : __('cpanel/posts.bulky_error_message') }}</strong></div>
        @endif
        @if (($update_message = Session::get('destroyed')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/posts.bulky_destroyed_message') : __('cpanel/posts.bulky_error_message') }}</strong></div>
        @endif

        {{-- Tab switch: published vs trashed --}}
        <div class="mb-4 inline-flex rounded-lg border border-ink-200 bg-surface p-1 shadow-sm">
            <a href="{{route('cpanel_posts_list')}}" class="rounded-md px-3.5 py-1.5 text-sm font-medium transition {{ !$is_trash ? 'bg-brand-600 text-white shadow-sm' : 'text-ink-600 hover:text-ink-900' }}">@lang('cpanel/posts.general_posts')</a>
            <a href="{{route('cpanel_trashed_posts_list')}}" class="rounded-md px-3.5 py-1.5 text-sm font-medium transition {{ $is_trash ? 'bg-brand-600 text-white shadow-sm' : 'text-ink-600 hover:text-ink-900' }}">@lang('cpanel/posts.trashed_posts')</a>
        </div>

        <div class="card overflow-hidden">
            <form method="POST" action="{{ $is_trash ? route('cpanel_posts_bulk_action') : route('cpanel_posts_bulk_delete') }}">
                @csrf
                @if($is_trash) @method('POST') @else @method('DELETE') @endif
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="select-cover mb-0">
                        <select id="inputState" name="posts_action" required class="form-control">
                            <option selected="selected">@lang('cpanel/posts.bulk_action_label')</option>
                            @if($is_trash)
                                <option value="destroy">@lang('cpanel/posts.bulk_action_destroy_label')</option>
                                <option value="restore">@lang('cpanel/posts.bulk_action_restore_label')</option>
                            @else
                                <option value="delete">@lang('cpanel/posts.bulk_action_delete_label')</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-ghost">@lang('cpanel/posts.bulk_action_apply_label')</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table users-table">
                        <thead>
                            <tr>
                                <th class="w-10"><input class="form-check-input" id="selectAll" name="allposts" type="checkbox" aria-label="Select all"></th>
                                <th class="w-12">№</th>
                                <th>@lang('cpanel/posts.table_name')</th>
                                <th>@lang('cpanel/posts.table_author')</th>
                                <th>@lang('cpanel/posts.table_publish_date')</th>
                                <th>@lang('cpanel/posts.table_status')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($posts_count = 0)
                        @forelse($posts_list as $post)
                            @php($posts_count++)
                            <tr>
                                <td><input class="form-check-input posts-checkbox-input" id="post_{{$post->id}}" name="posts[]" type="checkbox" value="{{$post->id}}" aria-label="Select post"></td>
                                <td class="text-ink-400">{{$posts_count}}</td>
                                <td>
                                    <span class="font-medium text-ink-900">{{$post->title}}</span>
                                    <span class="user_actions">
                                        @if (Auth::user()->can('manage_posts', 'App\Http\Models\UserRoles'))
                                            <a href="{{route('cpanel_edit_post', ['id' => $post->id, 'lang' => get_current_lang()])}}" target="_blank">@lang('cpanel/posts.edit_post')</a>
                                            <input type="hidden" class="deleted_post_id" value="{{$post->id}}" name="deleted_post_id">
                                            @if(!$is_trash)
                                                <button type="button" class="delete_post">@lang('cpanel/posts.delete_post')</button>
                                            @else
                                                <button type="button" class="destroy_post">@lang('cpanel/posts.destroy_post')</button>
                                                <a href="{{route('cpanel_restore_post', $post->id)}}" class="restore_post">@lang('cpanel/posts.restore_post')</a>
                                            @endif
                                        @endif
                                    </span>
                                </td>
                                <td>{{$post->author->username}}</td>
                                <td class="whitespace-nowrap text-ink-600">{{Carbon\Carbon::parse($post->created_at)->format('d.m.Y')}}</td>
                                <td>
                                    @if($post->status == 1)
                                        <span class="badge badge-success">@lang('cpanel/posts.status_published')</span>
                                    @else
                                        <span class="badge badge-muted">@lang('cpanel/posts.status_private')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-ink-400">@lang('cpanel/posts.not_found')</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $posts_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/posts.js_delete_confirmation')',
            destroy_confirmation = '@lang('cpanel/posts.js_destroy_confirmation')',
            delete_success = '@lang('cpanel/posts.js_delete_success')',
            destroy_success = '@lang('cpanel/posts.js_destroy_success')',
            error_message = '@lang('cpanel/posts.js_error')';
    </script>
    <script src="{{asset('admin')}}/js/post.js"></script>
@endpush
