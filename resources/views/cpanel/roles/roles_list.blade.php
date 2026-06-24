<?php
/**
 * Cmstack-Laravel
 * File: roles_list.blade.php
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
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/roles.list_headline')</h1>
            <a href="{{route('cpanel_add_user_role')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/roles.add_new_role')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if (Session::get('role_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/roles.role_added')</strong></div>
        @endif

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table users-table">
                    <thead>
                        <tr>
                            <th class="w-12">№</th>
                            <th>@lang('cpanel/roles.table_name')</th>
                            <th>@lang('cpanel/roles.table_action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($roles_count = 0)
                    @forelse($roles_list as $role)
                        @php($roles_count++)
                        <tr>
                            <td class="text-ink-400">{{$roles_count}}</td>
                            <td class="font-medium text-ink-900">{{$role->name}}</td>
                            <td>
                                <span class="user_actions">
                                    @if (Auth::user()->can('manage_user_roles', 'App\Http\Models\UserRoles'))
                                        <a href="{{route('cpanel_edit_user_role', $role->id)}}" target="_blank">@lang('cpanel/roles.edit')</a>
                                        @if($role->id !== 1 && $role->id !== 2)
                                            <input type="hidden" class="deleted_role_id" value="{{$role->id}}" name="deleted_role_id">
                                            <button type="button" class="delete_role">@lang('cpanel/roles.delete')</button>
                                        @endif
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-10 text-center text-ink-400">@lang('cpanel/roles.not_found')</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $roles_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/roles.js_delete_confirmation')',
            delete_success = '@lang('cpanel/roles.js_delete')',
            error_message = '@lang('cpanel/roles.js_error')';
    </script>
    <script src="{{asset('admin')}}/js/role.js"></script>
@endpush
