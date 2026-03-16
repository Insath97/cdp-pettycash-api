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
            $table->string('email')->unique();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('cascade');
            $table->date('date_needed');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('type', ['new_purchase', 'reimbursement'])->default('new_purchase');
            $table->string('receipt_image_path')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'verified', 'approved', 'rejected'])->default('pending');
            $table->enum('payment_status', ['pending', 'onhold', 'paid'])->default('pending');
            $table->foreignId('verified_by')->nullable()->index()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->index()->constrained('users')->onDelete('set null');
            $table->foreignId('paid_by')->nullable()->index()->constrained('users')->onDelete('set null');
            $table->text('verified_description')->nullable();
            $table->text('approved_description')->nullable();
            $table->text('rejected_description')->nullable();
            $table->text('payment_description')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
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
