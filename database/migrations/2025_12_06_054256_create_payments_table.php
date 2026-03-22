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

            // correct foreign keys
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('bill_id');

            // snapshot info
            $table->string('meter_no');

            $table->decimal('amount', 10, 2);

            $table->enum('payment_method', ['Cash', 'GCash'])
                  ->default('Cash');

            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Verified'])
                  ->default('Pending');

            // nullable because only required when payment_method = GCash
            $table->string('gcash_screenshot')->nullable();

            // optional remarks
            $table->text('message')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // constraints
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('bill_id')
                  ->references('id')
                  ->on('bills')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};