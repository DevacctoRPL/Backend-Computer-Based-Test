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
        Schema::create('jawaban_pesertas', function (Blueprint $table) {
            $table->id();
            $table->string('siswa_id');
            $table->string('tes_id');
            $table->string('soal_id');
            $table->string('jawaban');
            $table->text('jawaban_essay');
            $table->timestamps();

            //FK
            $table->index('jawaban');
            $table->foreign('jawaban')->references('jawaban_id')->on('pilihan_jawabans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_pesertas');
    }
};
