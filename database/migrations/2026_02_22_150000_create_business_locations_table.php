<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Business Locations ────────────────────────────────
        Schema::create('business_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')
                  ->constrained('businesses')
                  ->cascadeOnDelete();
            $table->string('location_id', 10)->unique();        // Auto-generated: BL0001, BL0002 …
            $table->string('name');
            $table->string('landmark')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->text('mobile')->nullable();                 // JSON array of mobile numbers
            $table->string('alternate_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Add FK on users.business_location_id ─────────────
        // The column was created in the users migration as a plain
        // unsignedBigInteger so we can add the constraint here, after
        // the business_locations table exists.
        // Nullable → Owner users have global (all-location) access.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('business_location_id')
                  ->references('id')
                  ->on('business_locations')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Drop FK from users first, then the table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_location_id']);
        });

        Schema::dropIfExists('business_locations');
    }
};
