<?php
/**
 * Laravella CMS
 * File: yourprofile.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/users.profile_headline')</h1>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/users.updated_success') : __('cpanel/users.updated_error') }}</strong></div>
        @endif

        <form action="{{ route('cpanel_update_user_profile', ['id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                {{-- Main form --}}
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/users.username')</label>
                                    <p class="rounded-lg bg-ink-50 px-3.5 py-2.5 text-sm font-medium text-ink-700">{{$user->username}}</p>
                                </div>
                                <div class="field">
                                    <label for="email" class="field-label">@lang('cpanel/users.email')</label>
                                    <input type="email" id="email" class="form-control" name="email" value="{{ old('email', $user->email) }}">
                                </div>
                                <div class="field">
                                    <label for="password" class="field-label">@lang('cpanel/users.new_password')</label>
                                    <input type="password" id="password" class="form-control" name="password" value="">
                                </div>
                                <div class="field">
                                    <label for="confirm_password" class="field-label">@lang('cpanel/users.new_password_confirmation')</label>
                                    <input type="password" id="confirm_password" class="form-control" name="password_confirmation" value="">
                                </div>
                                <div class="field">
                                    <label for="name" class="field-label">@lang('cpanel/users.name')</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
                                </div>
                                <div class="field">
                                    <label for="surname" class="field-label">@lang('cpanel/users.surname')</label>
                                    <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname', $user->surname) }}">
                                </div>
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/users.country')</label>
                                    <select name="country" id="country" class="form-control">
                                        @foreach($countries as $country)
                                            <option value="{{$country['name']}}" {{$country['name'] === $user->country ? 'selected' : ''}}>{{$country['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/users.city')</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $user->city) }}">
                                </div>
                            </div>

                            @if (Auth::user()->can('manage_users', 'App\Http\Models\UserRoles'))
                                <div class="field">
                                    <label class="field-label">@lang('cpanel/users.status')</label>
                                    <select name="role_id" id="user_role" class="form-control">
                                        @foreach($user_roles as $role)
                                            <option value="{{$role->id}}" {{$user->role->name === $role->name ? 'selected' : ''}}>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="field">
                                <label class="field-label">@lang('cpanel/users.about')</label>
                                <textarea rows="4" class="form-control" name="about_me">{{ old('about_me', $user->about_me) }}</textarea>
                            </div>

                            <fieldset class="mt-2 rounded-lg border border-ink-100 p-4">
                                <legend class="px-1 text-xs font-semibold uppercase tracking-wide text-ink-500">Social profiles</legend>
                                <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.facebook')</label>
                                        <input type="text" class="form-control" name="facebook_url" placeholder="https://" value="{{ old('facebook_url', $user->facebook_url) }}">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.google')</label>
                                        <input type="text" class="form-control" name="google_url" placeholder="https://" value="{{ old('google_url', $user->google_url) }}">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.twitter')</label>
                                        <input type="text" class="form-control" name="twitter_url" placeholder="https://" value="{{ old('twitter_url', $user->twitter_url) }}">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.instagram')</label>
                                        <input type="text" class="form-control" name="instagram_url" placeholder="https://" value="{{ old('instagram_url', $user->instagram_url) }}">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.linkedin')</label>
                                        <input type="text" class="form-control" name="linkedin_url" placeholder="https://" value="{{ old('linkedin_url', $user->linkedin_url) }}">
                                    </div>
                                    <div class="field">
                                        <label class="field-label">@lang('cpanel/users.xing')</label>
                                        <input type="text" class="form-control" name="xing_url" placeholder="https://" value="{{ old('xing_url', $user->xing_url) }}">
                                    </div>
                                </div>
                            </fieldset>

                            <div class="field">
                                <span class="field-label">@lang('cpanel/users.gender')</span>
                                <div class="flex flex-wrap gap-6">
                                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                        <input class="form-check-input" type="radio" name="gender" {{$user->gender === "male" ? 'checked' : null}} value="male" id="male"> Male
                                    </label>
                                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                                        <input class="form-check-input" type="radio" name="gender" {{$user->gender === "female" ? 'checked' : null}} value="female" id="female"> Female
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                            <button type="submit" class="btn btn-info">@lang('cpanel/users.update_button_label')</button>
                        </div>
                    </div>
                </div>

                {{-- Avatar / identity card --}}
                <div class="lg:col-span-1">
                    <div class="card card-user">
                        <div class="card-body text-center">
                            <span class="user-avatar block">
                                @if(!empty($user->avatar))
                                    <img id="file-image" class="avatar border-gray" src="{{$user->avatar}}" alt="User profile" />
                                @else
                                    <img id="file-image" class="avatar border-gray" src="{{asset('admin')}}/img/faces/noavatar.svg" type="file" name="fileUpload" accept="image/*" />
                                @endif
                                <span class="input-group-btn mt-3 inline-block">
                                    <a id="lfm" data-input="thumbnail" data-preview="holder" class="choose-image">
                                        @lang('cpanel/users.avatar_edit')
                                    </a>
                                </span>
                                <input id="file-upload" value="{{old('avatar', $user->avatar)}}" type="hidden" name="avatar" />
                            </span>
                            <h5 class="title mt-4 text-base font-semibold text-ink-900">{{$user->name}} {{$user->surname}}</h5>
                            <p class="description text-sm text-ink-500">{{$user->username}}</p>
                            @if($user->about_me)
                                <p class="description mt-3 text-sm leading-relaxed text-ink-600">{{$user->about_me}}</p>
                            @endif
                        </div>
                        @php
                            $socials = [
                                'facebook_url' => 'fa-facebook-square',
                                'google_url' => 'fa-google-plus-square',
                                'twitter_url' => 'fa-twitter',
                                'instagram_url' => 'fa-instagram',
                                'linkedin_url' => 'fa-linkedin-square',
                                'xing_url' => 'fa-xing-square',
                            ];
                            $has_social = collect($socials)->keys()->some(fn ($k) => !empty($user->$k));
                        @endphp
                        @if($has_social)
                            <div class="border-t border-ink-100">
                                <div class="button-container">
                                    @foreach($socials as $field => $icon)
                                        @if($user->$field)
                                            <a href="{{ old($field, $user->$field) }}" target="_blank" aria-label="{{ $field }}">
                                                <i class="fa {{ $icon }}"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('extrascripts')
    <script src="{{asset('')}}/vendor/laravel-filemanager/js/lfm.js"></script>
@endpush

@push('finalscripts')
    <script src="{{asset('admin')}}/js/user.js"></script>
    <script>
        var site_url = "<?php echo env('APP_URL'); ?>/";
    </script>
    <script src="{{asset('admin')}}/js/thumbnail.js"></script>
@endpush
