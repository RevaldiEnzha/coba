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
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_id')->constrained('laundry_orders')->cascadeOnDelete();

            $table->enum('type', ['jemput', 'antar']);
            $table->text('address');
            $table->decimal('distance_km', 8, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);

            $table->enum('status', [
                'menunggu_konfirmasi',
                'diproses',
                'selesai',
                'dibatalkan'
            ])->default('menunggu_konfirmasi');

            $table->timestamp('scheduled_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
