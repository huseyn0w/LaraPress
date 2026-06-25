<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;

/**
 * P8: send the email-verification notification on registration ONLY when the
 * "email_verification" general setting is enabled. Replaces Laravel's default
 * SendEmailVerificationNotification so a freshly registered user is not emailed
 * (or required to verify) while the feature is toggled off.
 */
class SendVerificationNotificationIfEnabled
{
    public function handle(Registered $event): void
    {
        if (! get_general_settings('email_verification')) {
            return;
        }

        if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            $event->user->sendEmailVerificationNotification();
        }
    }
}
