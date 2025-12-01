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
        Schema::table('shipments', function (Blueprint $table) {
            $table->date('estimated_delivery')->nullable()->after('status');
        });

        Schema::table('status_updates', function (Blueprint $table) {
            $table->string('proof_of_delivery')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('estimated_delivery');
        });

        Schema::table('status_updates', function (Blueprint $table) {
            $table->dropColumn('proof_of_delivery');
        });
    }
};
