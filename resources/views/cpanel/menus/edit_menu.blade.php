<?php
/**
 * Cmstack-Laravel
 * File: edit_menu.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 06.09.2019
 * Redesigned: DESIGN_SYSTEM §5 — x-card / x-field / x-button containers; token-driven.
 * Preserves:
 *   - form id="add_menu_form", @method("PUT"), field names title + slug + content (#menuContent)
 *   - .menu-box / .menu-list / .sortable / .ui-sortable / $existing_menu render_menu() output
 *   - .add_menu_item button type="button" — JS hook in menu.js
 *   - .create_menu on submit button
 *   - jQuery UI googleapis CDN link/script (sortable rework deferred — see report)
 *   - session flash + @include('cpanel.menus.partials.source-accordion')
 * NOTE: §5 keyboard-accessible sortable rework is DEFERRED (jQuery UI googleapis stays).
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
            <h1 class="text-xl font-semibold text-fg">@lang('cpanel/menus.edit_menu_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <x-alert variant="{{ $update_message ? 'success' : 'error' }}" class="mb-4">
                {{ $update_message ? __('cpanel/menus.menu_updated') : __('cpanel/menus.menu_error') }}
            </x-alert>
        @endif

        <form action="{{ route('cpanel_update_menu',['id' => $entity->id]) }}" id="add_menu_form" method="POST">
            @csrf
            @method("PUT")
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- Source panel --}}
                <div class="lg:col-span-1">
                    <x-card>
                        <x-slot:header>
                            <h2 class="text-sm font-semibold text-fg">@lang('cpanel/menus.edit_menu_headline')</h2>
                        </x-slot:header>

                        <div class="space-y-4">
                            @include('cpanel.core.translation')

                            <x-field label="@lang('cpanel/menus.menu_name')" name="title" :required="true">
                                <input type="text" id="menu_title" required class="form-control w-full" name="title" value="{{ old('title',$entity->title) }}">
                            </x-field>

                            <x-field label="@lang('cpanel/menus.menu_slug')" name="slug" :required="true">
                                <input type="text" id="cpanel_slug" required class="form-control w-full" name="slug" value="{{ old('slug', $entity->slug) }}">
                            </x-field>

                            @include('cpanel.menus.partials.source-accordion')

                            <x-button type="button" variant="outline" class="add_menu_item w-full">
                                @lang('cpanel/menus.add_to_menu')
                            </x-button>
                        </div>
                    </x-card>
                </div>

                {{-- Builder canvas --}}
                <div class="lg:col-span-2">
                    <x-card>
                        <x-slot:header>
                            <h2 class="text-sm font-semibold text-fg">@lang('cpanel/menus.list_headline')</h2>
                        </x-slot:header>

                        {{-- .menu-box preserves JS hooks; existing_menu rendered with .menu-list/.sortable classes --}}
                        <div class="menu-box min-h-[200px] rounded-md border border-dashed border-border bg-surface-2 p-3">
                            @if($existing_menu)
                                {!! $existing_menu !!}
                            @else
                                <ul class="menu-list sortable" id="sortable"></ul>
                            @endif
                        </div>
                        <input type="hidden" name="content" id="menuContent">

                        <x-slot:footer>
                            <div class="flex justify-end">
                                <x-button type="submit" variant="primary" class="create_menu">
                                    @lang('cpanel/menus.update_menu')
                                </x-button>
                            </div>
                        </x-slot:footer>
                    </x-card>
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
