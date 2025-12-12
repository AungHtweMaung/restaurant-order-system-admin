<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained()->onDelete('cascade');
            $table->string('session_token')->unique(); // to check session token for customers from each table
            $table->boolean('has_qr_verified')->default(false);
            $table->timestamp('qr_verified_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['session_token', 'has_qr_verified', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_sessions');
    }
};
