<?php
/**
 * Laravella CMS
 * File: new_role.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 11.08.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/roles.new_role_headline')</h1>
        </div>

        @include('cpanel.core.flash')

        <form action="{{ route('cpanel_save_user_role') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="field">
                        <label for="name" class="field-label">@lang('cpanel/roles.role_name')</label>
                        <input type="text" id="name" required class="form-control" name="name" value="{{ old('name') }}">
                    </div>

                    <fieldset class="mt-4 rounded-lg border border-ink-100 p-4">
                        <legend class="px-1 text-xs font-semibold uppercase tracking-wide text-ink-500">@lang('cpanel/roles.table_action')</legend>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($role_permissions as $permission)
                                @php($permission_name = str_replace('_', " ", $permission->name))
                                <label for="{{$permission->name}}" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                    <input class="form-check-input" id="{{$permission->name}}" name="permissions[]" value="{{$permission->name}}" type="checkbox">
                                    <span class="capitalize">{{$permission_name}}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/roles.add_new_role')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('finalscripts')
    <script src="{{asset('admin')}}/js/role.js"></script>
@endpush
