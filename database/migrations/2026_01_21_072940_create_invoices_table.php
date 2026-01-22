<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('invoice_date');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('exchange_rate_id')->nullable()->constrained();
            $table->foreignId('image_id')->nullable()->constrained('files');
            $table->foreignId('discount_id')->nullable()->constrained();
            $table->foreignId('delivery_fee_id')->nullable()->constrained();
            $table->decimal('vat_percent', 5, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
