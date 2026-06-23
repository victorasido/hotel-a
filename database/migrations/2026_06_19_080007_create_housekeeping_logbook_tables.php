<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('task_type', ['cleaning', 'inspection', 'maintenance', 'turndown'])->default('cleaning');
            $table->enum('status', ['pending', 'in_progress', 'done', 'cancelled'])->default('pending');
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->text('notes')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('log_book', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('shift', ['pagi', 'siang', 'malam'])->default('pagi');
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['normal', 'penting', 'urgent'])->default('normal');
            $table->date('log_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_book');
        Schema::dropIfExists('housekeeping_tasks');
    }
};
