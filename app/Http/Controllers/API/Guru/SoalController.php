<?php

namespace App\Http\Controllers\API\Guru;

use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Tes;

class SoalController extends Controller
{
    public function createSoal(Request $request, $tes_id) {
        $request->validate([
            'jenis_soal' => 'required|string',
            'pertanyaan' => 'required|string',
            'file_gambar'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'poin' => 'required|integer',
        ]);

        try {
            $user = auth()->user()->guru_profile_id;
            if ($user == null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum login'
                ], 400);
            };

            $soal_id = uniqid('soal_');

            $file_gambar = $request->file('file_gambar')->store('gambar_soal', 'public');
            $soal = Soal::create([
                'jenis_soal' => $request->jenis_soal,
                'pertanyaan' => $request->pertanyaan,
                'file_gambar' => $file_gambar,
                'poin' => $request->poin,
                'tes_id' => $tes_id,
                'soal_id' => $soal_id
            ]);

            return response()->json([
                'success'=> true,
                'data' => $soal->only('pertanyaan', 'soal_id', 'jenis_soal', 'file_gambar', 'poin')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function showSoal($tes_id) {
        try {
            $soal = Soal::where('tes_id', '=', $tes_id)->get();
            return response()->json([
                'success' => true,
                'data' => $soal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteAllSoal($tes_id) {
        try {
            $soals = Soal::where('tes_id', '=', $tes_id)->get();
            foreach ($soals as $soal) {
                $soal->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Semua soal sudah dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteSoalById(Request $request, $soal_id) {
        try {
            $soal = Soal::where("soal_id", '=', $soal_id)->first();

            if (!$soal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soal tidak ditemukan atau tidak termasuk dalam tes ini'
                ], 404);
            }

            $soal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Soal sukse dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateSoal(Request $request, $soal_id) {
        $request->validate([
            'jenis_soal' => 'nullable|string',
            'pertanyaan' => 'nullable|string',
            'file_gambar'=> 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'poin' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            $soal = Soal::where('soal_id', '=', $soal_id)->firstOrFail();

            $soal->update($request->only('jenis_soal', 'pertanyaan', 'poin'));

            // Menangani unggahan file jika gambar baru disediakan
            if ($request->hasFile('file_gambar')) {
                $file_gambar = $request->file('file_gambar')->store('gambar_soal', 'public');
                $soal->update(['file_gambar' => $file_gambar]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil diperbarui',
                'data' => $soal->only('pertanyaan', 'soal_id', 'jenis_soal', 'file_gambar', 'poin')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui soal',
                'data' => $e->getMessage()
            ], 400);
        }
    }


}
