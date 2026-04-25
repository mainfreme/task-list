<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE clients ALTER COLUMN nip DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE clients ALTER COLUMN nip SET NOT NULL');
    }
};
