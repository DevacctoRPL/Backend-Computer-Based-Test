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
        Schema::create('penilaian_essays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jawaban_peserta_id');
            $table->unsignedBigInteger('guru_id');
            $table->integer('nilai');
            $table->text('komentar')->nullable();
            $table->enum('status', ['belum_dinilai', 'sudah_dinilai'])->default('belum_dinilai');
            $table->timestamps();

            // Foreign keys
            $table->foreign('jawaban_peserta_id')->references('id')->on('jawaban_pesertas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('guru_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_essays');
    }
};
