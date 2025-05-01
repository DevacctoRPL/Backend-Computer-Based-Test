<?php

namespace App\Http\Controllers\API\Siswa;
use App\Http\Controllers\Controller;
use App\Models\SiswaProfile;

class SiswaController extends Controller
{
    public function getAllSiswa() {
        try {
            $siswa = SiswaProfile::all();
    
            if ($siswa->isEmpty()) {
                return response()->json([
                    "status" => false,
                    "message" => "Data Siswa Tidak Ditemukan",
                ], 404);
            }
    
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Diambil",
                "data" => $siswa
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan saat mengambil data.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSiswaById($siswa_id) {
        try {
            $siswa = SiswaProfile::where('user_id','=', $siswa_id)->first();
    
            if (!$siswa) {
                return response()->json([
                    "status" => false,
                    "message" => "Data Siswa Tidak Ditemukan",
                ], 404);
            }
    
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Diambil",
                "data" => $siswa
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Terjadi kesalahan.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function createSiswa(Request $request) {
        try {
            $siswa = SiswaProfile::create($request->all());
    
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Ditambahkan",
                "data" => $siswa
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Gagal menambahkan data.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function updateSiswa(Request $request, $siswa_id) {
        try {
            $siswa = SiswaProfile::where('user_id', $siswa_id)->first();
    
            if (!$siswa) {
                return response()->json([
                    "status" => false,
                    "message" => "Data Siswa Tidak Ditemukan",
                ], 404);
            }
    
            $siswa->update($request->all());
    
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Diupdate",
                "data" => $siswa
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Gagal mengupdate data.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroySiswa($siswa_id) {
        try {
            $siswa = SiswaProfile::find($siswa_id);
    
            if (!$siswa) {
                return response()->json([
                    "status" => false,
                    "message" => "Data Siswa Tidak Ditemukan",
                ], 404);
            }
    
            $siswa->delete();
    
            return response()->json([
                "status" => true,
                "message" => "Data Siswa Berhasil Dihapus",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Gagal menghapus data.",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    
}