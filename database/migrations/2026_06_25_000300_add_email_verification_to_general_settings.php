<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * P8: add the "email_verification" toggle to the general settings singleton.
 * When enabled, self-registered members must verify their email before they
 * can use member-only areas. Defaults to off to preserve existing behaviour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('email_verification')->default(0)->after('membership');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('email_verification');
        });
    }
};
