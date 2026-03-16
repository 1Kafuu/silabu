<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    public function indexAxios() {
        $barangs = Barang::all();
        
        $riwayat = DB::table('penjualan')
                    ->join('users', 'penjualan.iduser', '=', 'users.id')
                    ->select('penjualan.*', 'users.name')
                    ->orderBy('penjualan.timestamp', 'desc')
                    ->get();

        return view('admin.pos.index_axios', compact('barangs', 'riwayat'));
    }

    public function indexAjax() {
        $barangs = Barang::all();
        
        $riwayat = DB::table('penjualan')
                    ->join('users', 'penjualan.iduser', '=', 'users.id')
                    ->select('penjualan.*', 'users.name')
                    ->orderBy('penjualan.timestamp', 'desc')
                    ->get();

        return view('admin.pos.index_ajax', compact('barangs', 'riwayat'));
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            // 1. Simpan ke tabel penjualan
            $id_penjualan = DB::table('penjualan')->insertGetId([
                'timestamp' => now(),
                'total'     => $request->total_harga,
                'iduser'    => Auth::user()->id,
            ], 'id_penjualan');

            // 2. Simpan detail barang
            foreach ($request->items as $item) {
                DB::table('penjualan_detail')->insert([
                    'id_penjualan' => $id_penjualan,
                    'id_barang'    => $item['id_barang'],
                    'jumlah'       => $item['qty'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            DB::commit();
            return response()->json(['status' => 'success', 'msg' => 'Transaksi Berhasil!']);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return response()->json(['status' => 'error', 'msg' => $e->getMessage()], 500);
        }
    }
}
