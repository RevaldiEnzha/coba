<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();

            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('weight', 8, 2)->nullable();
            $table->integer('quantity')->nullable();

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            $table->enum('order_source', ['outlet', 'portal'])->default('outlet');
            $table->enum('delivery_option', ['ambil_sendiri', 'antar'])->default('ambil_sendiri');

            $table->enum('status', [
                'diterima',
                'dicuci',
                'dijemur',
                'disetrika',
                'siap_diambil',
                'selesai',
                'dibatalkan'
            ])->default('diterima');

            $table->enum('payment_status', ['belum_bayar', 'dibayar'])->default('belum_bayar');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};
