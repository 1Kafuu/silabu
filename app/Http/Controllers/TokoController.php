<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TokoController extends Controller
{
    public function index()
    {
        $toko = Toko::orderBy("idtoko", "asc")->get();
        $generator = new BarcodeGeneratorPNG();

        $toko->transform(function ($item) use ($generator) {
            $barcodeBiner = $generator->getBarcode($item->idtoko, $generator::TYPE_CODE_128);
            $item->barcode_base64 = base64_encode($barcodeBiner);
            return $item;
        });

        return view("admin.toko.toko", compact('toko'));
    }

    public function create()
    {
        return view('admin.toko.create-toko');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'latitude'   => 'required|numeric',
            'longtitude' => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        $toko = Toko::where('nama_toko', $validated['nama_toko'])->first();

        if ($toko) {
            session()->flash('error', 'Toko dengan nama yang sama sudah ada. Silakan coba lagi.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Toko::create([
            'nama_toko'  => $validated['nama_toko'],
            'latitude'   => $validated['latitude'],
            'longtitude' => $validated['longtitude'],
            'accuracy'   => $validated['accuracy'],
        ]);

        if ($result) {
            session()->flash('success', 'Toko berhasil ditambahkan!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('toko-list')
            ]);
        } else {
            session()->flash('error', 'Gagal menambahkan toko. Silakan coba lagi.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function edit($id)
    {
        $toko = Toko::where('idtoko', $id)->get();
        return view('admin.toko.update-toko', compact('toko'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'latitude'   => 'required|numeric',
            'longtitude' => 'required|numeric',
            'accuracy'   => 'required|numeric',
        ]);

        $result = Toko::where('idtoko', $id)->update([
            'nama_toko'  => $validated['nama_toko'],
            'latitude'   => $validated['latitude'],
            'longtitude' => $validated['longtitude'],
            'accuracy'   => $validated['accuracy'],
        ]);

        if ($result) {
            session()->flash('success', 'Toko berhasil diperbarui!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('toko-list')
            ]);
        } else {
            session()->flash('error', 'Gagal memperbarui toko. Silakan coba lagi.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function delete($id)
    {
        $toko = Toko::findOrFail($id);
        $toko->delete();

        return redirect()->route('toko-list')
            ->with('success', 'Toko berhasil dihapus!');
    }
}
