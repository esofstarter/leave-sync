<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('national_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('country'); // 'Macedonia' or 'Bulgaria'
            $table->year('year');
            $table->integer('leave_type_id')->default(5);
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('national_holidays');
    }
};
