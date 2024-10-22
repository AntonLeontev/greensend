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
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('label');
            $table->string('result_path')->nullable();
            $table->unsignedMediumInteger('initial_phones_number')->nullable();
            $table->unsignedMediumInteger('clean_phones_number')->nullable();
            $table->unsignedMediumInteger('whatsapp_phones_number')->nullable();
            $table->string('status')->nullable();
            $table->unsignedTinyInteger('whatsapp_check_percent')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
