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
        // Create enum type for address type if using PostgreSQL native enums

        DB::statement("
        DO $$
        BEGIN
            IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'address_type') THEN
                CREATE TYPE address_type AS ENUM ('billing', 'shipping', 'registered_office', 'delivery', 'other');
            END IF;
        END$$;
        ");
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_uuid');
            $table->string('street');
            $table->string('postal_code', 20);
            $table->string('city', 100);
            $table->string('state_province', 100);
            $table->string('country', 100);
            $table->text('additional_info');
            $table->string('house_number', 10);
            $table->string('apartment_number', 15);
            $table->string('type')->default('other'); // Using string for compatibility, enum type created above
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('added_at');
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
        DB::statement('DROP TYPE IF EXISTS address_type');
    }
};
