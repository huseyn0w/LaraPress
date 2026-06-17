<?php
/**
 * Laravella CMS
 * File: media.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 23.08.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
@endpush

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/media.headline')</h1>
        </div>

        <div class="card overflow-hidden">
            {{-- Laravel FileManager runs inside this iframe; the standalone-button
                 script (loaded below) drives the LFM picker hooks elsewhere. --}}
            <iframe src="/filemanager" class="block h-[70vh] min-h-[500px] w-full border-0"></iframe>
        </div>
    </div>
@endsection

@push('extrascripts')
    <script src="{{base_path('vendor')}}/laravel-filemanager/js/stand-alone-button.js"></script>
@endpush
