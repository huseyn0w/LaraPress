<?php
/**
 * Cmstack-Laravel
 * File: menus_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 04.09.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/menus.list_headline')</h1>
            <a href="{{route('cpanel_add_new_menu')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/menus.add_new_menu')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if (Session::get('menu_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/menus.menu_added')</strong></div>
        @endif

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table users-table">
                    <thead>
                        <tr>
                            <th class="w-12">№</th>
                            <th>@lang('cpanel/menus.table_name')</th>
                            <th>@lang('cpanel/menus.table_action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($menus_count = 0)
                    @forelse($menus_list as $menu)
                        @php($menus_count++)
                        <tr>
                            <td class="text-ink-400">{{$menus_count}}</td>
                            <td class="font-medium text-ink-900">{{$menu->title}}</td>
                            <td>
                                <span class="user_actions">
                                    @if (Auth::user()->can('manage_menus', 'App\Http\Models\UserRoles'))
                                        <a href="{{route('cpanel_edit_menu', ['id' => $menu->id, 'lang' => get_current_lang()])}}" target="_blank">@lang('cpanel/menus.edit')</a>
                                        <input type="hidden" class="deleted_menu_id" value="{{$menu->id}}" name="deleted_menu_id">
                                        @if($menu->id > 1)
                                            <button type="button" class="delete_menu">@lang('cpanel/menus.delete')</button>
                                        @endif
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-10 text-center text-ink-400">@lang('cpanel/menus.not_found')</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $menus_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/menus.js_delete_confirmation')',
            delete_success = '@lang('cpanel/menus.js_delete')',
            error_message = '@lang('cpanel/menus.js_error')';
    </script>
    <script src="{{asset('admin')}}/js/menu.js"></script>
@endpush
