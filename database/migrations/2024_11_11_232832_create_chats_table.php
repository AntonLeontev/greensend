<?php

use App\Models\Channel;
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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignIdFor(Channel::class)->constrained()->cascadeOnDelete();
            $table->string('phone');
            $table->unsignedBigInteger('active_distribution_id')->nullable();
            $table->unsignedSmallInteger('last_action_id')->nullable();

            $table->timestamps();

            $table->index(['channel_id', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
