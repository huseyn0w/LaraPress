<?php
/**
 * Cmstack-Laravel
 * File: header-styles.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 09.08.2019
 */
?>
<meta charset="utf-8" />
<link rel="apple-touch-icon" sizes="180x180" href="{{asset('front/'.config('app.template_name').'/img/apple-touch-icon.png')}}">
<link rel="icon" type="image/png" sizes="32x32" href="{{asset('front/'.config('app.template_name').'/img/favicon-32x32.png')}}">
<link rel="icon" type="image/png" sizes="16x16" href="{{asset('front/'.config('app.template_name').'/img/favicon-16x16.png')}}">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>@lang('cpanel/nav/top.header_title')</title>
<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
{{-- Font Awesome CDN removed (DESIGN_SYSTEM §3/§7 — no font/icon CDN).
     Social profile icons replaced with inline SVG in cpanel/users/profile.blade.php.
     Vendor laravel-filemanager views use FA icons internally; those are
     addressed in Phase 6 when the filemanager is replaced/isolated. --}}
@stack('extrastyles')
{{-- Tailwind admin bundle (Vite). Bootstrap + Light Bootstrap Dashboard assets
     are no longer loaded; the admin is fully Tailwind now. --}}
@vite(['resources/css/admin.css', 'resources/js/admin.js'])
