<?php

namespace App\Http\Controllers\API\Siswa;
<<<<<<< HEAD
use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;

class SiswaController extends Controller
{
    public function index() {
        $siswa = SiswaProfile::with("user")->get();
        return response()->json([
            "status" => true,
            "message" => "Data Siswa Berhasil Diambil",
            "data" => $siswa
        ], 200);
    }

    public function show($id) {
        $siswa = SiswaProfile::with("user")->find($id);
        if ($siswa) {
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Diambil",
                "data" => $siswa
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Data Siswa Tidak Ditemukan",
            ], 404);
        }
    }
    public function store(Request $request) {
        $siswa = SiswaProfile::create($request->all());
        return response()->json([
            "status" => true,
            "message" => "Data Siswa Berhasil Ditambahkan",
            "data" => $siswa
        ], 201);
    }
    public function update(Request $request, $id) {
        $siswa = SiswaProfile::find($id);
        if ($siswa) {
            $siswa->update($request->all());
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Diupdate",
                "data" => $siswa
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Data Siswa Tidak Ditemukan",
            ], 404);
        }
    }
    public function destroy($id) {
        $siswa = SiswaProfile::find($id);
        if ($siswa) {
            $siswa->delete();
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Dihapus",
            ], 200);
        } else {
            return response()->json([
                "status" => false,
                "message" => "Data Siswa Tidak Ditemukan",
            ], 404);
        }
    }
}
// Compare this snippet from app/Models/SiswaProfile.php:
=======

use App\Http\Controllers\Controller;
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
}
>>>>>>> 69307a7ead2062a6a9435f6ccbe64f01f4e7cc6d
