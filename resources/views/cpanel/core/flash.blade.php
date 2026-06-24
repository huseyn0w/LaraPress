<?php
/**
 * Cmstack-Laravel
 * File: flash.blade.php  (Phase 5)
 * Reusable validation-error block for admin forms. Replaces the repeated
 * `@if ($errors->any())` markup. Pull in with @include('cpanel.core.flash').
 */
?>
@if ($errors->any())
    <div class="alert alert-danger">
        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM9 7a1 1 0 1 1 2 0v3a1 1 0 1 1-2 0V7Zm1 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/></svg>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
