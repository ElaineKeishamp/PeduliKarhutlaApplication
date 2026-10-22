<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('profile_photo_path')->nullable()->after('phone');
            $table->string('bank_name')->nullable()->after('profile_photo_path');
            $table->string('bank_account_holder')->nullable()->after('bank_name');
            $table->string('bank_account_number', 50)->nullable()->after('bank_account_holder');
        });

        DB::table('users')->select('id', 'email')->orderBy('id')->get()->each(function ($user) {
            $base = Str::slug(Str::before($user->email, '@'), '');
            $userId = (string) $user->id;
            $prefix = $base !== '' ? $base : 'pengguna';
            $username = substr($prefix, 0, 30 - strlen($userId)).$userId;
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'profile_photo_path', 'bank_name', 'bank_account_holder', 'bank_account_number']);
        });
    }
};
