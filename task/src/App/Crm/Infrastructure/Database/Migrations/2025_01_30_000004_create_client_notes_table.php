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
        // Create enum type for note type if using PostgreSQL native enums
        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'note_type') THEN
                CREATE TYPE note_type AS ENUM ('note', 'call', 'meeting', 'email', 'task');
            END IF;
        END$$;
        ");

        Schema::create('client_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_uuid');
            $table->uuid('user_uuid'); // Who created the note
            $table->text('content');
            $table->string('type')->default('note'); // note, call, meeting, email, task
            $table->timestamps();

            // Foreign keys
            $table->foreign('client_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            $table->foreign('user_uuid')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');

            // Indexes for performance
            $table->index('client_uuid');
            $table->index('user_uuid');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_notes');
        DB::statement('DROP TYPE IF EXISTS note_type');
    }
};
