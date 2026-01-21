<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create enum type for relationship type if using PostgreSQL native enums
        DB::statement("CREATE TYPE relationship_type AS ENUM ('parent_company', 'subsidiary', 'partner', 'competitor')");

        Schema::create('client_relationships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_uuid'); // Parent client
            $table->uuid('child_uuid'); // Child/related client
            $table->string('relationship_type'); // parent_company, subsidiary, partner, competitor
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('parent_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('child_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            // Prevent self-referencing and duplicate relationships
            $table->unique(['parent_uuid', 'child_uuid', 'relationship_type']);

            // Indexes for performance
            $table->index('parent_uuid');
            $table->index('child_uuid');
            $table->index('relationship_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_relationships');
        DB::statement('DROP TYPE IF EXISTS relationship_type');
    }
};
