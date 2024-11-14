<?php

use App\Models\Distribution;
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
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['active_distribution_id']);

            $table->foreignIdFor(Distribution::class, 'active_distribution_id')
                ->nullable()
                ->after('phone')
                ->constrained('distributions', 'id')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Distribution::class, 'active_distribution_id');

            $table->unsignedBigInteger('active_distribution_id')->nullable();
        });
    }
};
