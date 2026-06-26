<?php
/**
 * Cmstack-Laravel
 * File: media.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 23.08.2019
 * Redesigned: DESIGN_SYSTEM §5 "File upload / dropzone" — dashed border-strong,
 *             surface-2 fill, icon + prompt per spec.
 * Preserves:  The Laravel FileManager iframe at /filemanager (primary file management UI).
 *             The stand-alone-button.js script (drives LFM picker hooks elsewhere in admin).
 *             The <meta name="csrf-token"> required by dropzone/LFM JS.
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-fg">@lang('cpanel/media.headline')</h1>
        </div>

        {{-- §5 dropzone styling: dashed border-strong, surface-2 fill, rounded-lg --}}
        {{-- The iframe IS the file manager (LFM runs inside it) --}}
        <div class="overflow-hidden rounded-lg border-2 border-dashed border-strong bg-surface-2">
            {{-- LFM prompt header — shown while iframe is loading / for context --}}
            <div class="flex items-center gap-3 border-b border-border bg-surface px-5 py-3">
                <x-icon name="upload" class="text-muted shrink-0" width="18" height="18" />
                <span class="text-sm text-muted">@lang('cpanel/media.headline')</span>
            </div>

            {{-- Laravel FileManager runs inside this iframe --}}
            <iframe
                src="/filemanager"
                class="block w-full border-0"
                style="height: 70vh; min-height: 500px;"
                title="@lang('cpanel/media.headline')"
            ></iframe>
        </div>
    </div>
@endsection

@push('extrascripts')
    <script src="{{base_path('vendor')}}/laravel-filemanager/js/stand-alone-button.js"></script>
@endpush
