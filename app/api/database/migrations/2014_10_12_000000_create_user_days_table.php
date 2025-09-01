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
        Schema::create('user_days', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('days_to_use')->nullable();
            $table->string('days_used')->nullable();
            $table->string('year')->unique();
            $table->boolean('if_filled')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_days');
    }
};
