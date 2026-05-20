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
        Schema::create('redmine_task_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_type_id')->unique();
            $table->integer('redmine_task_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('leave_type_id')
                  ->references('id')->on('leave_types')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redmine_task_mappings');
    }
};
