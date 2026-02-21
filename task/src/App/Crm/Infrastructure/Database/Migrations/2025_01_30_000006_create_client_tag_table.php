<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_tag', function (Blueprint $table) {
            $table->uuid('client_uuid');
            $table->uuid('client_tag_uuid');
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('client_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('client_tag_uuid')
                ->references('id')
                ->on('client_tags')
                ->onDelete('cascade');

            // Primary key (composite)
            $table->primary(['client_uuid', 'client_tag_uuid']);

            // Indexes
            $table->index('client_uuid');
            $table->index('client_tag_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_tag');
    }
};
