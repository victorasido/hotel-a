<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('restrict');
            $table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->foreignId('guest_id')->constrained()->onDelete('restrict');
            $table->dateTime('actual_check_in');
            $table->integer('extra_pax')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('checked_in_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained()->onDelete('restrict');
            $table->dateTime('actual_check_out');
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'debit', 'credit', 'other'])->default('cash');
            $table->text('notes')->nullable();
            $table->foreignId('checked_out_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_outs');
        Schema::dropIfExists('check_ins');
    }
};
