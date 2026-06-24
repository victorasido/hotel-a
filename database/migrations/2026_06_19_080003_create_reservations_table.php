<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->foreignId('guest_id')->constrained()->onDelete('restrict');
            $table->foreignId('room_id')->constrained()->onDelete('restrict');
            $table->date('check_in_date');
            $table->date('check_out_date');
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'sqlite') {
                $table->integer('nights')->storedAs('cast(julianday(check_out_date) - julianday(check_in_date) as integer)');
            } else {
                $table->integer('nights')->storedAs('DATEDIFF(check_out_date, check_in_date)');
            }
            $table->integer('pax')->default(1);
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('confirmed');
            $table->enum('source', ['walk_in', 'phone', 'online', 'travel_agent', 'other'])->default('walk_in');
            $table->decimal('room_rate', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->text('special_request')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
