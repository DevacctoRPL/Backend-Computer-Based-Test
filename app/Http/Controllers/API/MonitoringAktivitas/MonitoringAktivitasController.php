<?php


namespace App\Http\Controllers\API\MonitoringAktivitas;
use App\Models\MonitoringAktivitas;
use App\Models\SesiTes;
use App\Models\SiswaProfile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringAktivitasController extends Controller
{
   
    public function showAllMonitoringAktivitas()
{
    $data = MonitoringAktivitas::with('sesi')->get();

    return response()->json([
        "status" => true,
        "message" => "Data Monitoring Aktivitas berhasil diambil",
        "data" => $data
    ], 200);
}
    
    public function showMonitoringAktivitasbyId($monitoring_id)
        {
            try {
                $item = MonitoringAktivitas::where('id', '=', $monitoring_id)
                    ->with('sesi')
                    ->firstOrFail();


                return response()->json([
                    "status" => true,
                    "message" => "Data Monitoring Aktivitas berhasil diambil",
                    "data" => $item
                ], 200);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return response()->json([
                    "status" => false,
                    "message" => "Data tidak ditemukan"
                ], 404);
            }
        }

        

    public function createMonitoringAktivitas(Request $request)
        {
            try {
                $request->validate([
                    'sesi_id' => 'required|exists:sesi_tes,id',
                    'jenis_aktivitas' => 'required|string',
                    'waktu' => 'required|date',
                    'screenshot' => 'nullable|string|max:255',
                ]);
        
                $data = MonitoringAktivitas::create($request->all());
        
                if ($data) {
                    return response()->json([
                        'message' => 'Aktivitas berhasil ditambahkan',
                        'data' => $data
                    ], 201);
                } else {
                    return response()->json([
                        'message' => 'Aktivitas gagal ditambahkan'
                    ], 500);
                }
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat menambahkan aktivitas',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        

    public function updateMonitoringAktivitas(Request $request, $monitoring_id)
    {
        $data = MonitoringAktivitas::findOrFail($monitoring_id);

        $data->update($request->only([
            'jenis_aktivitas',
            'waktu',
            'screenshot',
        ]));

        return response()->json([
            'message' => 'Aktivitas berhasil diperbarui',
            'data' => $data
        ]);
    }
    public function destroyMonitoringAktivitas($monitoring_id)
    {
        $data = MonitoringAktivitas::where('id', $monitoring_id)->first();
        if (!$data) {
            return response()->json([
                'message' => 'Aktivitas tidak ditemukan'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Aktivitas berhasil dihapus'
        ]);
    }


    public function logAktivitasSiswa($sesi_id)
    {
        $sesi = SesiTes::with(['siswa', 'monitoringAktivitas'])->findOrFail($sesi_id);
        return response()->json($sesi);
    }
}
        
