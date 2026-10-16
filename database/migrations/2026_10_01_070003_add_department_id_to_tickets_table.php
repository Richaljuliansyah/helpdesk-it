<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('ticket_code')
                ->constrained('departments')->cascadeOnUpdate()->nullOnDelete();
            $table->index('department_id');
        });

        $itId = DB::table('departments')->where('name', 'IT')->value('id');
        if ($itId) {
            DB::table('tickets')->whereNull('department_id')->update(['department_id' => $itId]);
        }
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
