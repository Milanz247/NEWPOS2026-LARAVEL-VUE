<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('currency_symbol', 10)->default('$')->after('currency');
            $table->string('tax_number', 50)->nullable()->after('currency_symbol');
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('tax_number');
            $table->decimal('profit_margin_target', 5, 2)->default(0)->after('tax_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn(['currency_symbol', 'tax_number', 'tax_percentage', 'profit_margin_target']);
        });
    }
};
