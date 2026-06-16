<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_requests', 'service_id')) {
                $table->foreignId('service_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('services')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('delivery_requests', 'note')) {
                $table->text('note')->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_requests', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_requests', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }

            if (Schema::hasColumn('delivery_requests', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
