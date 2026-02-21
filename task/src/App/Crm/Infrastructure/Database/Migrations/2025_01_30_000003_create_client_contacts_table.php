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
        // Create enum types for contact type and role if using PostgreSQL native enums
        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'contact_type') THEN
                CREATE TYPE contact_type AS ENUM ('email', 'phone', 'mobile', 'fax', 'website', 'other');
            END IF;
        END$$;
        ");
        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'contact_role') THEN
                CREATE TYPE contact_role AS ENUM ('billing', 'technical', 'admin', 'sales', 'other');
            END IF;
        END$$;
        ");

        Schema::create('client_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_uuid');
            $table->string('type'); // email, phone, mobile, fax, website, other
            $table->string('value');
            $table->string('country_prefix', 5)->nullable(); // Optional, only for phones
            $table->string('contact_role')->nullable(); // billing, technical, admin, sales, other
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            // Foreign key
            $table->foreign('client_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('client_uuid');
            $table->index('type');
            $table->index('is_primary');
            $table->index('is_active');
            $table->index('contact_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
        DB::statement('DROP TYPE IF EXISTS contact_type');
        DB::statement('DROP TYPE IF EXISTS contact_role');
    }
};
