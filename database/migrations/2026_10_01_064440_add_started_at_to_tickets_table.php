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
            $table->timestamp('started_at')->nullable()->after('agent_id');
        });

        // Tiket yang sedang diproses: perkirakan mulai dikerjakan saat terakhir diperbarui
        DB::table('tickets')
            ->where('status', 'diproses')
            ->whereNull('started_at')
            ->update(['started_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('started_at');
        });
    }
};
