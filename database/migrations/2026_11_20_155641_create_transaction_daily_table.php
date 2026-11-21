<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_daily', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
            $table->date('date')->index();
            $table->decimal('open', 15, 2)->nullable();
            $table->decimal('high', 15, 2)->nullable();
            $table->decimal('low', 15, 2)->nullable();
            $table->decimal('close', 15, 2)->nullable();
            $table->bigInteger('volume')->default(0);
            $table->timestamps();

            // Mencegah duplikasi data saham pada tanggal yang sama
            $table->unique(['company_id', 'date'], 'uq_transaction_daily_company_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_daily');
    }
};
