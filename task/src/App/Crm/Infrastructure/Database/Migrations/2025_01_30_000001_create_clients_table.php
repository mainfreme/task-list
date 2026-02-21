<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create enum type for client status if using PostgreSQL native enums
        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'client_status') THEN
                CREATE TYPE client_status AS ENUM ('lead','prospect','active','inactive','archived');
            END IF;
        END$$;
        ");

        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('address_uuid')->nullable();
            $table->string('name');
            $table->string('nip');
            $table->string('regon')->nullable();
            $table->string('pesel')->nullable();
            $table->string('country');
            $table->string('status')->default('lead'); // Using string for compatibility, enum type created above
            $table->string('source')->nullable(); // Source of client (marketing, referral, etc.)
            $table->integer('rating')->nullable(); // Rating 1-5
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('next_contact_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->boolean('is_company')->default(false);
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index('address_uuid');
            $table->index('status');
            $table->index('source');
            $table->index('is_delete');
            $table->index('is_company');
            $table->index('created_at');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        DB::statement('DROP TYPE IF EXISTS client_status');
    }
};
