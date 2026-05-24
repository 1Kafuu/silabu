<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\NFC;
use Illuminate\Http\Request;

class NFCController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('student.user')
            ->whereDate('scan_time', today())
            ->orderBy('scan_time', 'desc')
            ->get();

        return view('admin.attendance.index', compact('attendances'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string',
        ]);

        $serial = strtoupper(trim($request->serial_number));

        $card = NFC::with('student.user')->where('serial_number', $serial)->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu NFC tidak terdaftar.',
            ], 404);
        }

        // Prevent duplicate attendance on the same day
        $alreadyScanned = Attendance::where('student_id', $card->student_id)
            ->whereDate('scan_time', today())
            ->exists();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa sudah absen hari ini.',
                'student' => $card->student->user->name ?? '-',
            ], 409);
        }

        $attendance = Attendance::create([
            'student_id' => $card->student_id,
            'scan_time'  => now(),
            'status'     => 'hadir',
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Absensi berhasil dicatat.',
            'student'    => $card->student->user->name ?? '-',
            'nim'        => $card->student->NIM ?? '-',
            'scan_time'  => $attendance->scan_time->format('H:i:s'),
        ]);
    }
}
