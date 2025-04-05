<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilTes extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tes_id',
        'nilai',
        'status_lulus',
        'durasi_pengerjaan',
        'tanggal_submit',
        'komentar',
        'guru_id',
        'jawaban_peserta_id'
    ];

    public function siswa()
    {
        return $this->belongsTo(SiswaProfile::class, 'siswa_id');
    }

    public function tes()
    {
        return $this->belongsTo(Tes::class, 'tes_id');
    }

    public function guru()
    {
        return $this->belongsTo(GuruProfile::class, 'guru_id');
    }

    public function jawabanPeserta()
    {
        return $this->belongsTo(JawabanPeserta::class, 'jawaban_peserta_id');
    }
}
