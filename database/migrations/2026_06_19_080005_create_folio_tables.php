<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_folios', function (Blueprint $table) {
            $table->id();
            $table->string('folio_number', 20)->unique();
            $table->foreignId('check_in_id')->constrained()->onDelete('restrict');
            $table->foreignId('guest_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('folio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folio_id')->constrained('guest_folios')->onDelete('cascade');
            $table->enum('type', ['room', 'fnb', 'extra', 'discount', 'laundry', 'transport'])->default('extra');
            $table->string('description');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->date('item_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('folio_items');
        Schema::dropIfExists('guest_folios');
    }
};
