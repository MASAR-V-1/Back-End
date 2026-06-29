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
        Schema::create('user_invitations', function (Blueprint $table) {
            $table->id();
            // ربط الدعوة بالمنظمة لضمان العزل والأمان الكامل (Multi-tenancy)
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('email')->unique(); // البريد الإلكتروني المدعو
            $table->string('token')->unique(); // التوكن العشوائي المشفر
            $table->timestamp('expires_at');   // تاريخ انتهاء صلاحية الرابط الأمنية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_invitations');
    }
};
