<?php
/**
 * Laravella CMS
 * File: edit_menu.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 06.09.2019
 */
?>

@extends('cpanel.core.index')

@push('extrastyles')
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
@endpush

@php
    $menu_params = [
        'menu_type' => "list",
        'menu_class' => "menu-list sortable ui-sortable",
    ];
    $existing_menu = render_menu(json_decode($entity->content), $menu_params);
@endphp

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/menus.edit_menu_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/menus.menu_updated') : __('cpanel/menus.menu_error') }}</strong></div>
        @endif

        <form action="{{ route('cpanel_update_menu',['id' => $entity->id]) }}" id="add_menu_form" method="POST">
            @csrf
            @method("PUT")
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                {{-- Source panel --}}
                <div class="lg:col-span-1">
                    <div class="card">
                        <div class="card-header"><h2 class="card-title">@lang('cpanel/menus.edit_menu_headline')</h2></div>
                        <div class="card-body">
                            @include('cpanel.core.translation')
                            <div class="field">
                                <label for="menu_title" class="field-label">@lang('cpanel/menus.menu_name')</label>
                                <input type="text" id="menu_title" required class="form-control" name="title" value="{{ old('title',$entity->title) }}">
                            </div>
                            <div class="field">
                                <label for="cpanel_slug" class="field-label">@lang('cpanel/menus.menu_slug')</label>
                                <input type="text" required class="form-control" name="slug" value="{{ old('slug', $entity->slug) }}">
                            </div>

                            @include('cpanel.menus.partials.source-accordion')

                            <button type="button" class="btn btn-ghost add_menu_item mt-4 w-full">@lang('cpanel/menus.add_to_menu')</button>
                        </div>
                    </div>
                </div>

                {{-- Builder canvas --}}
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header"><h2 class="card-title">@lang('cpanel/menus.list_headline')</h2></div>
                        <div class="card-body">
                            <div class="menu-box">
                                @if($existing_menu)
                                    {!! $existing_menu !!}
                                @else
                                    <ul class="menu-list sortable" id="sortable"></ul>
                                @endif
                            </div>
                            <input type="hidden" name="content" id="menuContent">
                        </div>
                        <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                            <button type="submit" class="btn btn-info create_menu">@lang('cpanel/menus.update_menu')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('extrascripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <script src="{{asset('admin')}}/js/jquery.mjs.nestedSortable.js"></script>
@endpush

@push('finalscripts')
    <script src="{{asset('admin')}}/js/menu.js"></script>
@endpush
