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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_key_hash');
            $table->string('request_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('ip_whitelist')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('api_key_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

