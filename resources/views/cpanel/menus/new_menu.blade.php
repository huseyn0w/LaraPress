<?php
/**
 * Cmstack-Laravel
 * File: new_menu.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 04.09.2019
 * Redesigned: DESIGN_SYSTEM §5 — x-card / x-field / x-button containers; token-driven.
 * Preserves:
 *   - form id="add_menu_form", field names title + slug + content (hidden #menuContent)
 *   - .menu-box / .menu-list / .sortable / #sortable — hooks for menu.js + jQuery UI sortable
 *   - .add_menu_item button type="button" — JS hook in menu.js
 *   - .create_menu on submit button
 *   - jQuery UI googleapis CDN link/script (sortable rework deferred — see report)
 *   - @include('cpanel.menus.partials.source-accordion') for the source panel
 *   - @push('extrascripts') + @push('finalscripts') for sortable + menu.js
 * NOTE: §5 keyboard-accessible sortable rework is DEFERRED (jQuery UI googleapis stays).
 */
?>

@extends('cpanel.core.index')

@push('extrastyles')
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
@endpush

@php
    $form_action = route('cpanel_save_new_menu');
    if(!empty(request()->route('id')))  $form_action = route('cpanel_save_new_menu', ['id' => request()->route('id')]);
@endphp

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-fg">@lang('cpanel/menus.new_menu_headline')</h1>
        </div>

        @include('cpanel.core.flash')

        <form action="{{ $form_action }}" id="add_menu_form" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- Source panel --}}
                <div class="lg:col-span-1">
                    <x-card>
                        <x-slot:header>
                            <h2 class="text-sm font-semibold text-fg">@lang('cpanel/menus.new_menu_headline')</h2>
                        </x-slot:header>

                        <div class="space-y-4">
                            @include('cpanel.core.translation')

                            <x-field label="@lang('cpanel/menus.menu_name')" name="title" :required="true">
                                <input type="text" id="menu_title" required class="form-control w-full" name="title" value="{{ old('title') }}">
                            </x-field>

                            <x-field label="@lang('cpanel/menus.menu_slug')" name="slug" :required="true">
                                <input type="text" id="cpanel_slug" required class="form-control w-full" name="slug" value="{{ old('slug') }}">
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

                        {{-- .menu-box and #sortable/.menu-list/.sortable preserved for menu.js + jQuery UI --}}
                        <div class="menu-box min-h-[200px] rounded-md border border-dashed border-border bg-surface-2 p-3">
                            <ul class="menu-list sortable" id="sortable"></ul>
                        </div>
                        <input type="hidden" name="content" id="menuContent">

                        <x-slot:footer>
                            <div class="flex justify-end">
                                <x-button type="submit" variant="primary" class="create_menu">
                                    @lang('cpanel/menus.create_menu')
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
