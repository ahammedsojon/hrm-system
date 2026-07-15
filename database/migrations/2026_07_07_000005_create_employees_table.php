<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('gender')->nullable();
            $table->string('profile_photo')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('active');
            $table->date('joining_date')->nullable();
            $table->date('probation_start_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->foreignId('employment_type_id')->nullable()->constrained('employment_types')->nullOnDelete();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_no')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();

            $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('department_head_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['department_head_id']);
        });

        Schema::dropIfExists('employees');
    }
};
