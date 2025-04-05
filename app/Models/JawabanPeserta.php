<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanPeserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tes_id',
        'soal_id',
        'jawaban',
        'jawaban_essay'
    ];

    public function siswa()
    {
        return $this->belongsTo(SiswaProfile::class, 'siswa_id');
    }

    public function tes()
    {
        return $this->belongsTo(Tes::class, 'tes_id');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class, 'jawaban');
    }

    public function penilaianEssay()
    {
        return $this->hasOne(PenilaianEssay::class, 'jawaban_peserta_id');
    }
}
