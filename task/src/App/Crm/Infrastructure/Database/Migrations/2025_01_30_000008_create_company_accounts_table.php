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
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('address_uuid')->nullable(); // Changed from client_address to address
            $table->uuid('client_uuid');
            $table->string('name');
            $table->string('number');
            $table->string('swift_code');
            $table->string('iban');
            $table->string('bic');
            $table->string('account_name');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('address_uuid')
                ->references('id')
                ->on('addresses')
                ->onDelete('set null'); // Changed from client_address to addresses

            $table->foreign('client_uuid')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');

            // Indexes for performance
            $table->index('address_uuid');
            $table->index('client_uuid');
            $table->index('is_active');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_accounts');
    }
};
