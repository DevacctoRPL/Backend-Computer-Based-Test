<?php

namespace App\Http\Controllers\API\Guru;

use App\Http\Controllers\Controller;
use App\Models\JawabanPeserta;
use App\Models\PenilaianEssay;
use App\Models\Soal;
use App\Models\Tes;
use App\Models\HasilTes;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PenilaianEssayController extends Controller
{
    /**
     * Get all tests for the authenticated teacher
     */
    public function getTests()
    {
        $guru_id = Auth::user()->guru_profile_id;
        $tests = Tes::where('guru_id', $guru_id)->get();

        return response()->json([
            'success' => true,
            'data' => $tests
        ]);
    }

    /**
     * Get all essay answers for a specific test
     */
    public function getEssayAnswers($tes_id)
    {
        $tes = Tes::findOrFail($tes_id);

        // Verify the current teacher owns this test
        if (Auth::user()->guru_profile_id != $tes->guru_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Get all essay questions for this test
        $soalEssay = Soal::where('tes_id', $tes_id)
                        ->where('jenis_soal', 'essay')
                        ->get();

        if ($soalEssay->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No essay questions found for this test'
            ]);
        }

        // Get all student answers for these questions
        $jawabanPeserta = JawabanPeserta::whereIn('soal_id', $soalEssay->pluck('id'))
                                      ->whereNotNull('jawaban_essay')
                                      ->with(['siswa', 'soal', 'penilaianEssay'])
                                      ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tes' => $tes,
                'jawaban' => $jawabanPeserta
            ]
        ]);
    }

    /**
     * Grade a single essay answer
     */
    public function gradeEssay(Request $request, $jawaban_id)
    {
        $validator = Validator::make($request->all(), [
            'nilai' => 'required|integer|min:0|max:100',
            'komentar' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jawaban = JawabanPeserta::findOrFail($jawaban_id);

        // Verify the current teacher owns the test this answer belongs to
        $tes = Tes::findOrFail($jawaban->tes_id);
        if (Auth::user()->guru_profile_id != $tes->guru_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $guru_id = Auth::user()->guru_profile_id;

        // Create or update assessment
        $penilaian = PenilaianEssay::updateOrCreate(
            ['jawaban_peserta_id' => $jawaban_id],
            [
                'guru_id' => $guru_id,
                'nilai' => $request->nilai,
                'komentar' => $request->komentar,
                'status' => 'sudah_dinilai'
            ]
        );

        // Update HasilTes if it exists
        $this->updateHasilTes($jawaban->siswa_id, $jawaban->tes_id);

        return response()->json([
            'success' => true,
            'data' => $penilaian,
            'message' => 'Essay graded successfully'
        ]);
    }

    /**
     * Batch grade multiple essay answers
     */
    public function batchGradeEssays(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penilaian' => 'required|array',
            'penilaian.*.jawaban_id' => 'required|exists:jawaban_pesertas,id',
            'penilaian.*.nilai' => 'required|integer|min:0|max:100',
            'penilaian.*.komentar' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $guru_id = Auth::user()->guru_profile_id;
        $results = [];
        $updatedTests = [];

        foreach ($request->penilaian as $item) {
            $jawaban = JawabanPeserta::findOrFail($item['jawaban_id']);

            // Verify the current teacher owns the test this answer belongs to
            $tes = Tes::findOrFail($jawaban->tes_id);
            if (Auth::user()->guru_profile_id != $tes->guru_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to one or more answers'
                ], 403);
            }

            // Create or update assessment
            $penilaian = PenilaianEssay::updateOrCreate(
                ['jawaban_peserta_id' => $item['jawaban_id']],
                [
                    'guru_id' => $guru_id,
                    'nilai' => $item['nilai'],
                    'komentar' => $item['komentar'] ?? null,
                    'status' => 'sudah_dinilai'
                ]
            );

            $results[] = $penilaian;

            // Track which tests need updating
            if (!in_array($jawaban->tes_id, $updatedTests)) {
                $updatedTests[$jawaban->siswa_id] = $jawaban->tes_id;
            }
        }

        // Update HasilTes for each affected student and test
        foreach ($updatedTests as $siswa_id => $tes_id) {
            $this->updateHasilTes($siswa_id, $tes_id);
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'message' => 'Batch grading completed successfully'
        ]);
    }

    /**
     * Update the HasilTes record for a student's test
     */
    private function updateHasilTes($siswa_id, $tes_id)
    {
        $hasilTes = HasilTes::where('siswa_id', $siswa_id)
                           ->where('tes_id', $tes_id)
                           ->first();

        if ($hasilTes) {
            // Recalculate total score based on all answers
            $totalNilai = $this->calculateTotalScore($siswa_id, $tes_id);
            $hasilTes->nilai = $totalNilai;
            $hasilTes->save();
        }
    }

    /**
     * Calculate total score for a student's test
     */
    private function calculateTotalScore($siswa_id, $tes_id)
    {
        // Get all questions for this test
        $soal = Soal::where('tes_id', $tes_id)->get();
        $totalPoin = $soal->sum('poin');
        $nilaiSiswa = 0;

        // Calculate score for multiple choice questions
        $mcAnswers = JawabanPeserta::where('siswa_id', $siswa_id)
                                  ->where('tes_id', $tes_id)
                                  ->whereNotNull('jawaban')
                                  ->get();

        foreach ($mcAnswers as $answer) {
            $isCorrect = PilihanJawaban::where('id', $answer->jawaban)
                                      ->where('is_benar', true)
                                      ->exists();
            if ($isCorrect) {
                $soalPoin = Soal::find($answer->soal_id)->poin;
                $nilaiSiswa += $soalPoin;
            }
        }

        // Calculate score for essay questions
        $essayAnswers = JawabanPeserta::where('siswa_id', $siswa_id)
                                     ->where('tes_id', $tes_id)
                                     ->whereNotNull('jawaban_essay')
                                     ->get();

        foreach ($essayAnswers as $answer) {
            $penilaian = PenilaianEssay::where('jawaban_peserta_id', $answer->id)->first();
            if ($penilaian && $penilaian->status == 'sudah_dinilai') {
                $soalPoin = Soal::find($answer->soal_id)->poin;
                $nilaiSiswa += ($penilaian->nilai / 100) * $soalPoin;
            }
        }

        // Convert to percentage
        return $totalPoin > 0 ? round(($nilaiSiswa / $totalPoin) * 100) : 0;
    }
}
