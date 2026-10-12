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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Financials
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('bulky_handling_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            // Customer Info
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('street_address');
            $table->string('barangay');
            $table->string('city_municipality');
            $table->string('province');
            $table->string('postal_code');
            $table->string('region');

            // GCash / Payment
            $table->string('gcash_sender_number')->nullable();
            $table->string('gcash_reference_number')->nullable();
            
            // Statuses
            $table->enum('payment_status', ['pending_verification', 'verified', 'failed'])->default('pending_verification');
            $table->enum('fulfillment_status', ['unfulfilled', 'fulfilled', 'shipped'])->default('unfulfilled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
