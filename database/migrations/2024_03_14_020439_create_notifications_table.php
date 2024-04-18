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
        Schema::create('notifications', function (Blueprint $table) {
            $table->string('id')
                ->index()
                ->primary()
                ->unique();
            $table->timestamps();
            $table->softDeletes();
            $table->string('user_id');
            $table->string('type');
            $table->string('title');
            $table->string('message');
            $table->boolean('is_read')->default(true);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_back_at')->nullable();
            $table->timestamp('pushed_at')->nullable();
            $table->string('notifiable_id')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->json('payload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
