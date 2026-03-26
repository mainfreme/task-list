<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('chart_definitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('chart_type');
            $table->json('display_fields');
            $table->text('sql_query');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_definitions');
    }
};
