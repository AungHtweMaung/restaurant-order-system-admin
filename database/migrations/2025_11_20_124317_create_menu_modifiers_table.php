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
        Schema::create('menu_modifiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('modifier_id');
            // Optional: make each modifier unique for a menu item
            $table->unique(['menu_id', 'modifier_id']);
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('modifier_id')->references('id')->on('modifiers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_modifiers');
    }
};
