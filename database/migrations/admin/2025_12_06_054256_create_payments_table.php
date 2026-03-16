<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->unsignedBigInteger('bill_id');
            $table->string('meter_no');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['Cash', 'GCash'])->default('Cash');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Verified'])->default('Pending');
            $table->string('gcash_screenshot')->nullable();
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
