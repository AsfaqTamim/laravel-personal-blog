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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('device_type', 20); // desktop | mobile | tablet
            $table->string('browser', 40)->nullable();
            $table->string('network_type', 20)->nullable(); // wifi | cellular | null = unknown
            $table->string('country', 80)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('page_url', 255)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['ip_address', 'visited_at']);
            $table->index('device_type');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
