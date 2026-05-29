<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['discount_amount', 'discount_reason', 'discount_breakdown']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
            $table->string('discount_reason')->nullable()->after('gross_profit');
            $table->json('discount_breakdown')->nullable()->after('discount_reason');
        });
    }
};
