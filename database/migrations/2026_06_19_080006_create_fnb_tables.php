<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fnb_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon', 10)->nullable(); // emoji icon
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fnb_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('fnb_categories')->onDelete('restrict');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('is_available')->default(true);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('fnb_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('check_in_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('folio_id')->nullable()->constrained('guest_folios')->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('order_type', ['room_service', 'restaurant', 'takeaway'])->default('room_service');
            $table->enum('status', ['pending', 'processing', 'served', 'cancelled'])->default('pending');
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
        });

        Schema::create('fnb_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('fnb_orders')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('fnb_menus')->onDelete('restrict');
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fnb_order_items');
        Schema::dropIfExists('fnb_orders');
        Schema::dropIfExists('fnb_menus');
        Schema::dropIfExists('fnb_categories');
    }
};
