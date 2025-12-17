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
    Schema::table('don_hang', function (Blueprint $table) {
        $table->string('email_nguoi_nhan', 191)->nullable()->after('dia_chi_giao');
    });
}

public function down(): void
{
    Schema::table('don_hang', function (Blueprint $table) {
        $table->dropColumn('email_nguoi_nhan');
    });
}
};
