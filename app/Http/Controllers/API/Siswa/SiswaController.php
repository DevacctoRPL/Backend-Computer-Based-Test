<?php

namespace App\Http\Controllers\API\Siswa;
use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function sendJawabanPilihanGanda(Request $request, $tes_id, $soal_id, $jawaban_id) {
        $siswa = auth()->user()->siswa_profile_id;

        try {
            DB::beginTransaction();

            $existingJawaban = DB::table('jawaban_pesertas')
                ->where('siswa_id', $siswa)
                ->where('tes_id', $tes_id)
                ->where('soal_id', $soal_id)
                ->where('jawaban', $jawaban_id)
                ->first();

            if ($existingJawaban) {
                DB::rollBack();

                return response()->json([
                    'success' => true,
                    'message' => 'Jawaban sudah ada'
                ], 200);
            }

            $pilihanJawaban = DB::table('pilihan_jawabans')
                ->where('jawaban_id', $jawaban_id)
                ->where('soal_id', $soal_id)
                ->first();

            if (!$pilihanJawaban) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban yang diberikan tidak memiliki soal_id yang sesuai'
                ], 400);
            }

            DB::table('jawaban_pesertas')->insert([
                "siswa_id" => $siswa,
                "tes_id" => $tes_id,
                "soal_id" => $soal_id,
                "jawaban" => $jawaban_id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil dikirim'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendJawabanEssay(Request $request, $tes_id, $soal_id) {
        $siswa = auth()->user()->siswa_profile_id;

        try {
            DB::beginTransaction();

            DB::table('jawaban_pesertas')->insert([
                "siswa_id" => $siswa,
                "tes_id" => $tes_id,
                "soal_id" => $soal_id,
                "jawaban" => $request->jawaban
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil dikirim'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function seeAllTes() {
        $tes = DB::table('tes')->get();
        $data = [];
        foreach($tes as $t) {
            $kelas = json_decode($t->kelas);
            $data[] = [
                "id" => $t->id,
                "tes_id" => $t->tes_id,
                "kode_tes" => $t->kode_tes,
                "judul" => $t->judul,
                "deskripsi" => $t->deskripsi,
                "jam_mulai" => $t->jam_mulai,
                "durasi_menit" => $t->durasi_menit,
                "tanggal_mulai" => $t->tanggal_mulai,
                "tanggal_selesai" => $t->tanggal_selesai,
                "batas_percobaan" => $t->batas_percobaan,
                "password_tes" => $t->password_tes,
                "semester" => $t->semester,
                "jenis_ujian" => $t->jenis_ujian,
                "mapel" => $t->mapel,
                "kelas" => $kelas
            ];
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ], 200);
    }

    public function seeTesById($tes_id) {
        $tes = DB::table('tes')->where('tes_id', '=', $tes_id)->first();
        $kelas = json_decode($tes->kelas);
        return response()->json([
            'success' => true,
            'data' => [
                "id" => $tes->id,
                "kode_tes" => $tes->kode_tes,
                "judul" => $tes->judul,
                "deskripsi" => $tes->deskripsi,
                "jam_mulai" => $tes->jam_mulai,
                "durasi_menit" => $tes->durasi_menit,
                "tanggal_mulai" => $tes->tanggal_mulai,
                "tanggal_selesai" => $tes->tanggal_selesai,
                "batas_percobaan" => $tes->batas_percobaan,
                "password_tes" => $tes->password_tes,
                "semester" => $tes->semester,
                "jenis_ujian" => $tes->jenis_ujian,
                "mapel" => $tes->mapel,
                "kelas" => $kelas
            ]
        ], 200);
    }
}
