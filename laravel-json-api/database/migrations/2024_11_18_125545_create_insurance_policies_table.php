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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_no')->index();
            $table->enum('policy_type', ['Health', 'Auto', 'Life', 'Property', 'Travel'])->index();
            $table->foreignId('customer_id');
            $table->decimal('premium_amount', 15);
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->enum('status', ['Pending', 'Active', 'Expired'])->default('Pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
