<?php
/**
 * Cmstack-Laravel
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
                            // Inline SVG icons replace Font Awesome 4 (CDN removed, DESIGN_SYSTEM §3/§7).
                            // Phase 6 introduces x-icon component; for now these minimal paths suffice.
                            $socials = [
                                'facebook_url'  => ['label' => 'Facebook',  'svg' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>'],
                                'google_url'    => ['label' => 'Google',    'svg' => '<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>'],
                                'twitter_url'   => ['label' => 'X (Twitter)', 'svg' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.73-8.835L1.254 2.25H8.08l4.259 5.63L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/>'],
                                'instagram_url' => ['label' => 'Instagram', 'svg' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>'],
                                'linkedin_url'  => ['label' => 'LinkedIn',  'svg' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>'],
                                'xing_url'      => ['label' => 'Xing',      'svg' => '<path d="M18.188 0c-.517 0-.741.325-.927.66 0 0-7.455 13.224-7.702 13.657.015.024 4.919 9.023 4.919 9.023.17.308.436.66.967.66h3.454c.211 0 .375-.078.463-.22.089-.151.089-.346-.009-.536l-4.879-8.916c-.004-.006-.004-.016 0-.022L22.139.756c.095-.191.097-.387.006-.535C22.056.078 21.894 0 21.686 0h-3.498zM3.648 4.74c-.211 0-.385.074-.473.216-.09.149-.078.339.02.531l2.34 4.05c.004.01.004.016 0 .021L1.86 16.051c-.099.188-.093.381 0 .529.085.142.239.234.45.234h3.461c.518 0 .766-.348.945-.667l3.734-6.609-2.378-4.155c-.172-.313-.434-.643-.962-.643H3.648z"/>'],
                            ];
                            $has_social = collect($socials)->keys()->some(fn ($k) => !empty($user->$k));
                        @endphp
                        @if($has_social)
                            <div class="border-t border-ink-100">
                                <div class="button-container">
                                    @foreach($socials as $field => $meta)
                                        @if($user->$field)
                                            <a href="{{ old($field, $user->$field) }}" target="_blank" aria-label="{{ $meta['label'] }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true">{!! $meta['svg'] !!}</svg>
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
