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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('nopol')->unique(); // Nomor Polisi
            $table->string('merek');
            $table->string('model')->nullable();
            $table->year('tahun_pembuatan');
            $table->enum('status', ['operasional', 'perbaikan', 'tidak aktif'])->default('operasional');

            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
