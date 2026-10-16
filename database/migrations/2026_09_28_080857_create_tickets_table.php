<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 20)->unique();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('priority', ['rendah', 'sedang', 'tinggi', 'urgent'])->default('sedang');
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditutup'])->default('baru');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
