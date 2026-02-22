<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Drop old foreign keys & columns ──────────
        if (Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        // ── Step 2: Drop old tables ──────────────────────────
        Schema::dropIfExists('branches');

        // ── Step 3: Drop old business_settings columns that will be restructured
        if (Schema::hasTable('business_settings')) {
            Schema::dropIfExists('business_settings');
        }

        // ── Step 4: Create businesses table (global settings) ─
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                           // shop/company name
            $table->string('currency', 10)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->enum('currency_symbol_placement', ['before', 'after'])->default('before');
            $table->string('logo')->nullable();
            $table->string('timezone')->default('UTC');

            // Financial settings
            $table->string('tax_number', 50)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('default_profit_percent', 5, 2)->default(25);

            // System settings
            $table->date('start_date')->nullable();
            $table->unsignedTinyInteger('fy_start_month')->default(1);        // 1=January … 12=December
            $table->enum('stock_accounting_method', ['FIFO', 'LIFO'])->default('FIFO');
            $table->unsignedSmallInteger('transaction_edit_days')->default(30);
            $table->string('date_format', 20)->default('m/d/Y');
            $table->string('time_format', 10)->default('12');                 // '12' or '24'
            $table->unsignedTinyInteger('currency_precision')->default(2);
            $table->unsignedTinyInteger('quantity_precision')->default(2);

            $table->boolean('is_setup_completed')->default(false);
            $table->timestamps();
        });

        // ── Step 5: Create business_locations table ──────────
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('location_id', 10)->unique();                      // BL0001, BL0002…
            $table->string('name');
            $table->string('landmark')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('mobile')->nullable();                               // JSON array of mobile numbers
            $table->string('alternate_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Step 6: Add business_location_id to users ────────
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('business_location_id')
                  ->nullable()
                  ->after('password')
                  ->constrained('business_locations')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_location_id']);
            $table->dropColumn('business_location_id');
        });

        Schema::dropIfExists('business_locations');
        Schema::dropIfExists('businesses');

        // Re-create old tables
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('currency')->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->string('tax_number', 50)->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('profit_margin_target', 5, 2)->default(0);
            $table->boolean('is_setup_completed')->default(false);
            $table->string('logo')->nullable();
            $table->string('timezone')->default('UTC');
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('password')->constrained('branches')->nullOnDelete();
        });
    }
};
