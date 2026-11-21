<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_hourly', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('company')->onDelete('cascade');
            $table->timestamp('timestamp')->index();
            $table->decimal('open', 15, 2)->nullable();
            $table->decimal('high', 15, 2)->nullable();
            $table->decimal('low', 15, 2)->nullable();
            $table->decimal('close', 15, 2)->nullable();
            $table->bigInteger('volume')->default(0);
            $table->timestamps();

            // Mencegah duplikasi data saham pada jam/timestamp yang sama
            $table->unique(['company_id', 'timestamp'], 'uq_transaction_hourly_company_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_hourly');
    }
};
