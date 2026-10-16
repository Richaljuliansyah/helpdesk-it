<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('name')
                ->constrained('departments')->cascadeOnUpdate()->nullOnDelete();
        });

        $itId = DB::table('departments')->where('name', 'IT')->value('id');
        if ($itId) {
            DB::table('categories')->whereNull('department_id')->update(['department_id' => $itId]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
