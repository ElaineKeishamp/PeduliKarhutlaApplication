<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('affected_type', 20)->default('manusia')->after('affected_people');
        });

        DB::table('categories')->updateOrInsert(
            ['slug' => 'perlindungan-satwa'],
            ['name' => 'Perlindungan Satwa', 'created_at' => now(), 'updated_at' => now()],
        );
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('affected_type');
        });
    }
};
