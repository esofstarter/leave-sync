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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->unsignedBigInteger('request_to'); 
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->integer('status')->nullable()->default(0);
            $table->integer('days')->nullable()->default(0);
            $table->string('reason')->nullable()->default("-");
            $table->integer('confirmed_by')->nullable();
            $table->integer('is_confirmed')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('leave_type_id')
                  ->references('id')->on('leave_types')
                  ->onDelete('cascade');

            $table->foreign('request_to')
                  ->references('id')->on('users')
                  ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['leave_type_id']);
            $table->dropForeign(['request_to']); 
        });

        Schema::dropIfExists('leave_requests');
    }
};
