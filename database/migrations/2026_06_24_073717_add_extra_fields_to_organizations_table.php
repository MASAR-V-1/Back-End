<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('region')->nullable()->after('description');
            $table->string('organization_type')->nullable()->after('region');
            $table->boolean('agreed_to_terms')->default(false)->after('organization_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['region', 'organization_type', 'agreed_to_terms']);
        });
    }

};
