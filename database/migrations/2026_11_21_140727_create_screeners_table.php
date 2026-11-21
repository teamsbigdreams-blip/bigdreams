<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screeners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
            $table->date('date');
            $table->string('pattern_name', 50)->default('HOURLY_DYNAMIC_HH_HL');
            $table->decimal('close', 15, 2)->nullable();

            $table->decimal('swing_low_price', 15, 2)->nullable();
            $table->dateTime('swing_low_price_date')->nullable();
            $table->bigInteger('swing_low_price_volume')->nullable();

            $table->decimal('swing_high_price', 15, 2)->nullable();
            $table->dateTime('swing_high_price_date')->nullable();
            $table->bigInteger('swing_high_price_volume')->nullable();

            $table->timestamps();

            $table->unique(['date', 'company_id', 'pattern_name'], 'uq_screeners_date_company_pattern');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screeners');
    }
};
