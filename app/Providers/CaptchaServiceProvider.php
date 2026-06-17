<?php

namespace App\Providers;

use App\Services\Captcha\CaptchaService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register the captcha service.
     *
     * Bound under the "captcha" key so the legacy app('captcha')->render()
     * calls in the Blade views keep working without modification.
     */
    public function register(): void
    {
        $this->app->singleton('captcha', function ($app) {
            return new CaptchaService($app['config']->get('captcha', []));
        });

        $this->app->alias('captcha', CaptchaService::class);
    }

    /**
     * Register the "captcha" validation rule.
     *
     * Passes automatically when captcha is disabled; otherwise verifies the
     * submitted token server-side via the captcha service.
     */
    public function boot(): void
    {
        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            return $this->app->make('captcha')->verify($value);
        }, 'The captcha verification failed.');
    }
}
