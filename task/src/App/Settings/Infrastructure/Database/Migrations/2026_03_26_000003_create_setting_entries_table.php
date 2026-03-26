<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('setting_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('group_key');
            $table->string('field_key');
            $table->string('field_type');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['group_key', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_entries');
    }
};
