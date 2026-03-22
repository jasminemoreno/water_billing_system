<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            $table->string('customer_name');

            $table->string('email')->unique();

            $table->string('password');

            // nullable profile fields
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth')->nullable();
            $table->string('avatar')->nullable();

            // 1 customer = 1 meter
            $table->string('meter_no')->unique();

            // account status
            $table->enum('status', ['Active', 'Inactive'])
                  ->default('Active');

            // login remember token
            $table->rememberToken();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};