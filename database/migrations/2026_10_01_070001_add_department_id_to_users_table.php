<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('role')
                ->constrained('departments')->cascadeOnUpdate()->nullOnDelete();
        });

        // Admin departemen yang sudah ada dialokasikan ke departemen IT
        $itId = DB::table('departments')->where('name', 'IT')->value('id');
        if ($itId) {
            DB::table('users')
                ->where('role', 'agent')
                ->whereNull('department_id')
                ->update(['department_id' => $itId]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
