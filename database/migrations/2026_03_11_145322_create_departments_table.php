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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // DEP001
            $table->string('description')->nullable();
            $table->string('phone_number')->nullable(); // Department phone number
            $table->string('department_head_name')->nullable(); // Name of department head
            $table->string('department_head_email')->nullable(); // Email of department head
            $table->string('department_head_phone')->nullable(); // Phone of department head
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
