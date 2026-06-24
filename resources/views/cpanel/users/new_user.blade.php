<?php
/**
 * Cmstack-Laravel
 * File: new_user.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 11.08.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/users.new_user_headline')</h1>
        </div>

        @include('cpanel.core.flash')

        <form action="{{ route('cpanel_save_new_user') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                        <div class="field">
                            <label for="username" class="field-label">@lang('cpanel/users.username')</label>
                            <input type="text" id="username" class="form-control" name="username" value="{{ old('username') }}">
                        </div>
                        <div class="field">
                            <label for="email" class="field-label">@lang('cpanel/users.email')</label>
                            <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="field">
                            <label for="password" class="field-label">@lang('cpanel/users.password')</label>
                            <input type="password" id="password" class="form-control" name="password" value="">
                        </div>
                        <div class="field">
                            <label for="confirm_password" class="field-label">@lang('cpanel/users.confirm_password')</label>
                            <input type="password" id="confirm_password" class="form-control" name="password_confirmation" value="">
                        </div>
                        <div class="field">
                            <label for="name" class="field-label">@lang('cpanel/users.name')</label>
                            <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        <div class="field">
                            <label for="surname" class="field-label">@lang('cpanel/users.surname')</label>
                            <input type="text" id="surname" class="form-control" name="surname" value="{{ old('surname') }}">
                        </div>
                        <div class="field">
                            <label class="field-label">@lang('cpanel/users.country')</label>
                            <select name="country" id="country" class="form-control">
                                @foreach($countries as $country)
                                    <option value="{{$country['name']}}">{{$country['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">@lang('cpanel/users.city')</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">@lang('cpanel/users.status')</label>
                        <select name="role_id" id="user_role" class="form-control">
                            @foreach($user_roles as $role)
                                <option value="{{$role['id']}}">{{$role['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label class="field-label">@lang('cpanel/users.about')</label>
                        <textarea rows="4" class="form-control" name="about_me" placeholder="Here can be your description">{{ old('about_me') }}</textarea>
                    </div>

                    <div class="field">
                        <span class="field-label">@lang('cpanel/users.gender')</span>
                        <div class="flex flex-wrap gap-6">
                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                <input class="form-check-input" type="radio" name="gender" value="male" id="male"> Male
                            </label>
                            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                <input class="form-check-input" type="radio" name="gender" value="female" id="female"> Female
                            </label>
                        </div>
                    </div>

                    <fieldset class="mt-2 rounded-lg border border-ink-100 p-4">
                        <legend class="px-1 text-xs font-semibold uppercase tracking-wide text-ink-500">Social profiles</legend>
                        <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.facebook')</label>
                                <input type="text" class="form-control" name="facebook_url" placeholder="https://" value="{{ old('facebook_url') }}">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.google')</label>
                                <input type="text" class="form-control" name="google_url" placeholder="https://" value="{{ old('google_url') }}">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.twitter')</label>
                                <input type="text" class="form-control" name="twitter_url" placeholder="https://" value="{{ old('twitter_url') }}">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.instagram')</label>
                                <input type="text" class="form-control" name="instagram_url" placeholder="https://" value="{{ old('instagram_url') }}">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.linkedin')</label>
                                <input type="text" class="form-control" name="linkedin_url" placeholder="https://" value="{{ old('linkedin_url') }}">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.xing')</label>
                                <input type="text" class="form-control" name="xing_url" placeholder="https://" value="{{ old('xing_url') }}">
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/users.add_new_user')</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('extrascripts')
    <script src="{{asset('admin')}}/js/userprofile.js"></script>
@endpush

@push('finalscripts')
    <script src="{{asset('admin')}}/js/user.js"></script>
@endpush
