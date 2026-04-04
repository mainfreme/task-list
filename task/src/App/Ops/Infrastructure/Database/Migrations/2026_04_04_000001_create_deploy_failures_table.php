<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('deploy_failures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('project', 255);
            $table->string('repository', 500);
            $table->string('container', 255)->nullable();
            $table->string('stage', 32);
            $table->text('message');
            $table->string('hostname', 255)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deploy_failures');
    }
};
