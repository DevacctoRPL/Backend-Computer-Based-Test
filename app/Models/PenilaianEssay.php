<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianEssay extends Model
{
    use HasFactory;

    protected $fillable = [
        'jawaban_peserta_id',
        'guru_id',
        'nilai',
        'komentar',
        'status'
    ];

    public function jawabanPeserta()
    {
        return $this->belongsTo(JawabanPeserta::class, 'jawaban_peserta_id');
    }

    public function guru()
    {
        return $this->belongsTo(GuruProfile::class, 'guru_id');
    }
}
