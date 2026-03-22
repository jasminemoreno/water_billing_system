<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {

            $table->id();

            // correct foreign key type
            $table->unsignedBigInteger('customer_id');

            // meter number (NOT unique — many bills per meter)
            $table->string('meter_no');

            // better precision
            $table->decimal('consumption', 10, 2);

            $table->decimal('total', 10, 2);

            // important billing fields
            $table->date('billing_date');
            $table->date('due_date');

            // status control
            $table->enum('status', ['Pending', 'Paid', 'Unpaid'])
                  ->default('Unpaid');

            // optional field
            $table->date('disconnection_date');

            // soft delete
            $table->softDeletes();

            // created_at & updated_at
            $table->timestamps();

            // foreign key constraint
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};