<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('need_id')->nullable()->constrained('campaign_needs')->nullOnDelete();
            $table->unsignedInteger('qty')->nullable();
            $table->unsignedBigInteger('amount');
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('payment_method', ['transfer', 'qris', 'ewallet']);
            $table->string('payment_ref')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
