<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['keluhan', 'saran', 'pertanyaan'])->default('keluhan');
            $table->string('subject', 150);
            $table->text('message');
            $table->enum('status', ['baru', 'ditangani'])->default('baru');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};
