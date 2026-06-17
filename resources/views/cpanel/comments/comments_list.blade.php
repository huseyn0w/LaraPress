<?php
/**
 * Laravella CMS
 * File: comments_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 08.11.2019
 */
?>
@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/comments.list_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('deleted')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/comments.bulky_deleted_message') : __('cpanel/comments.bulky_deleted_error_message') }}</strong></div>
        @endif

        <div class="card overflow-hidden">
            <form method="POST" action="{{route('cpanel_comments_bulk_delete')}}">
                @csrf
                @method('DELETE')
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="select-cover mb-0">
                        <select id="inputState" name="comments_action" required class="form-control">
                            <option selected="selected">@lang('cpanel/comments.bulk_action_label')</option>
                            <option value="delete">@lang('cpanel/comments.bulk_action_delete_label')</option>
                        </select>
                        <button type="submit" class="btn btn-ghost">@lang('cpanel/comments.bulk_action_apply')</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table users-table">
                        <thead>
                            <tr>
                                <th class="w-10"><input class="form-check-input" id="selectAll" name="allcomments" type="checkbox" aria-label="Select all"></th>
                                <th>@lang('cpanel/comments.table_title')</th>
                                <th>@lang('cpanel/comments.table_name')</th>
                                <th>@lang('cpanel/comments.table_author')</th>
                                <th>@lang('cpanel/comments.table_publish_date')</th>
                                <th>@lang('cpanel/comments.table_status')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($comments_count = 0)
                        @forelse($comments_list as $comment)
                            @php($comments_count++)
                            <tr>
                                <td><input class="form-check-input comments-checkbox-input" id="comment_{{$comment->id}}" name="comments[]" type="checkbox" value="{{$comment->id}}" aria-label="Select comment"></td>
                                <td class="text-ink-600">{{$comment->post->title}}</td>
                                <td>
                                    <span class="text-ink-800">{{$comment->comment}}</span>
                                    <span class="user_actions">
                                        @if (Auth::user()->can('manage_comments', 'App\Http\Models\UserRoles'))
                                            <button type="button" class="delete_comment">@lang('cpanel/comments.delete')</button>
                                            @if($comment->status !== 1)
                                                <button type="button" class="approve_comment">@lang('cpanel/comments.approve')</button>
                                            @else
                                                <button type="button" class="unapprove_comment">@lang('cpanel/comments.unapprove')</button>
                                            @endif
                                            <input type="hidden" class="action_comment_id" value="{{$comment->id}}" name="deleted_comment_id">
                                        @endif
                                    </span>
                                </td>
                                <td>{{$comment->user->username}}</td>
                                <td class="whitespace-nowrap text-ink-600">{{$comment->created_at->format('d.m.Y')}}</td>
                                <td>
                                    @if($comment->status == 1)
                                        <span class="badge badge-success">@lang('cpanel/comments.status_approved')</span>
                                    @else
                                        <span class="badge badge-muted">@lang('cpanel/comments.status_pending')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-ink-400">@lang('cpanel/comments.not_found')</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $comments_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var  _token = '{{ csrf_token() }}',
            delete_confirmation = '@lang('cpanel/comments.js_delete_confirmation')',
            approve_confirmation = '@lang('cpanel/comments.js_approve_confirmation')',
            unapprove_confirmation = '@lang('cpanel/comments.js_unapprove_confirmation')',
            approve_success = '@lang('cpanel/comments.js_approve')',
            delete_success = '@lang('cpanel/comments.js_delete')',
            unapprove_success = '@lang('cpanel/comments.js_unapprove')',
            error_message = '@lang('cpanel/comments.js_error')';
    </script>
    <script src="{{asset('admin')}}/js/comments.js"></script>
@endpush
