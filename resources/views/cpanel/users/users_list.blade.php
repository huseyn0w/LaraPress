<?php
/**
 * LaraPress CMS
 * File: users_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 09.08.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/users.list_headline')</h1>
            <a href="{{route('cpanel_add_new_user')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/users.add_new_user')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if ($update_message = Session::get('message'))
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $update_message ? __('cpanel/users.bulky_deleted_message') : __('cpanel/users.bulky_deleted_error_message') }}</strong>
            </div>
        @endif
        @if (Session::get('user_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/users.user_added')</strong></div>
        @endif

        <div class="card overflow-hidden">
            <form method="POST" action="{{route('cpanel_users_bulk_delete')}}">
                @csrf
                @method('DELETE')
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="select-cover mb-0">
                        <select id="inputState" name="users_action" required class="form-control">
                            <option selected="selected">@lang('cpanel/users.bulk_action_label')</option>
                            <option value="delete">@lang('cpanel/users.bulk_action_delete_label')</option>
                        </select>
                        <button type="submit" class="btn btn-ghost">@lang('cpanel/users.bulk_action_apply')</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table users-table">
                        <thead>
                            <tr>
                                <th class="w-10">
                                    <input class="form-check-input" id="selectAll" name="allusers" type="checkbox" aria-label="Select all">
                                </th>
                                <th class="w-12">№</th>
                                <th>@lang('cpanel/users.table_username')</th>
                                <th>@lang('cpanel/users.table_email')</th>
                                <th>@lang('cpanel/users.table_name')</th>
                                <th>@lang('cpanel/users.table_surname')</th>
                                <th>@lang('cpanel/users.table_country')</th>
                                <th>@lang('cpanel/users.table_city')</th>
                                <th>@lang('cpanel/users.table_role')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($users_count = 0)
                        @forelse($users_list as $user)
                            @php($users_count++)
                            <tr>
                                <td>
                                    <input class="form-check-input users-checkbox-input" id="user_{{$user->id}}" name="users[]" type="checkbox" value="{{$user->id}}" aria-label="Select user">
                                </td>
                                <td class="text-ink-400">{{$users_count}}</td>
                                <td>
                                    <span class="font-medium text-ink-900">{{$user->username}}</span>
                                    <span class="user_actions">
                                        @if (Auth::user()->can('manage_users', 'App\Http\Models\UserRoles'))
                                            <a href="{{route('cpanel_edit_user_profile', $user->id)}}" target="_blank">@lang('cpanel/users.edit_user')</a>
                                            <input type="hidden" class="deleted_user_id" value="{{$user->id}}" name="deleted_user_id">
                                            <button type="button" class="delete_user">@lang('cpanel/users.delete_user')</button>
                                        @endif
                                    </span>
                                </td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->surname}}</td>
                                <td>{{$user->country}}</td>
                                <td>{{$user->city}}</td>
                                <td><span class="badge badge-muted">{{$user->role->name}}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="py-10 text-center text-ink-400">@lang('cpanel/users.not_found')</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $users_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/users.js_delete_confirmation')',
            delete_success = '@lang('cpanel/users.js_delete_success')',
            delete_error = '@lang('cpanel/users.js_delete_error')';
    </script>
    <script src="{{asset('admin')}}/js/user.js"></script>
@endpush
