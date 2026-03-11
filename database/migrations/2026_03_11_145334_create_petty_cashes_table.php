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
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('full_name');
            $table->string('branch_location');
            $table->string('department')->nullable();
            $table->date('date_needed');
            $table->text('description')->nullable();
            $table->enum('type', ['new_purchase', 'reimbursement'])->default('new_purchase');
            $table->string('receipt_image_path')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['pending', 'onhold', 'paid'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};
