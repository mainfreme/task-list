  <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('website_url');
            $table->text('description');
            $table->string('phone');
            $table->string('email');
            $table->text('address');
            $table->string('status')->default('pending');
            $table->uuid('application_manager_id')->nullable();
            $table->foreign('application_manager_id')
                ->references('id')
                ->on('applications')
                ->nullOnDelete();
            $table->dateTime('due_date')->nullable();
            $table->text('delivery_address')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('application_manager_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

