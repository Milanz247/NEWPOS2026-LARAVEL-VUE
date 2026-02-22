<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            // ── Business profile ─────────────────────────────
            $table->string('name');
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->enum('currency_symbol_placement', ['before', 'after'])->default('before');
            $table->string('logo')->nullable();
            $table->string('timezone')->default('UTC');

            // ── Financial settings ───────────────────────────
            $table->string('tax_number', 50)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('default_profit_percent', 5, 2)->default(25);

            // ── System settings ──────────────────────────────
            $table->date('start_date')->nullable();
            $table->unsignedTinyInteger('fy_start_month')->default(1);        // 1 = January … 12 = December
            $table->enum('stock_accounting_method', ['FIFO', 'LIFO'])->default('FIFO');
            $table->unsignedSmallInteger('transaction_edit_days')->default(30);
            $table->string('date_format', 20)->default('m/d/Y');
            $table->string('time_format', 10)->default('12');                 // '12' or '24'
            $table->unsignedTinyInteger('currency_precision')->default(2);
            $table->unsignedTinyInteger('quantity_precision')->default(2);

            $table->boolean('is_setup_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
