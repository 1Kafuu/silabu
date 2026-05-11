<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Toko;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function dashboard()
    {
        $riwayat = Sales::with('toko')
            ->whereDate('waktu', today())
            ->orderBy('waktu', 'desc')
            ->get();

        return view('sales.dashboard', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idtoko'    => 'required|integer',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy'  => 'required|numeric',
            'jarak'     => 'required|integer',
            'status'    => 'required|string|max:20',
            'waktu'     => 'required',
        ]);

        $result = Sales::create([
            'idtoko'    => $validated['idtoko'],
            'latitude'  => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'accuracy'  => $validated['accuracy'],
            'jarak'     => $validated['jarak'],
            'status'    => $validated['status'],
            'waktu'     => $validated['waktu'],
        ]);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Kunjungan berhasil dicatat',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan kunjungan',
        ], 500);
    }

    public function findByBarcode($id)
    {
        $toko = Toko::where('idtoko', $id)->first();

        if (!$toko) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'idtoko'     => $toko->idtoko,
                'nama_toko'  => $toko->nama_toko,
                'latitude'   => $toko->latitude,
                'longtitude' => $toko->longtitude,
                'accuracy'   => $toko->accuracy,
            ],
        ]);
    }
}
