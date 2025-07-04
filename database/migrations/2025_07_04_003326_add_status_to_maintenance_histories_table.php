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
    Schema::table('maintenance_histories', function (Blueprint $table) {
        // Tambahkan kolom status setelah 'tanggal_servis_berikutnya'
        // Nilai defaultnya adalah 'Scheduled'
        $table->string('status')->default('Scheduled')->after('tanggal_servis_berikutnya');
    });
}

public function down(): void
{
    Schema::table('maintenance_histories', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
