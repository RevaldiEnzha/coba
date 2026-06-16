<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_requests', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('customers')
                    ->cascadeOnDelete();
            }
        });

        Schema::table('delivery_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['laundry_order_id']);
            } catch (\Throwable $e) {
                //
            }
        });

        Schema::table('delivery_requests', function (Blueprint $table) {
            $table->foreignId('laundry_order_id')
                ->nullable()
                ->change();

            $table->foreign('laundry_order_id')
                ->references('id')
                ->on('laundry_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('delivery_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['customer_id']);
                $table->dropForeign(['laundry_order_id']);
            } catch (\Throwable $e) {
                //
            }

            if (Schema::hasColumn('delivery_requests', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
        });
    }
};
