<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::orderBy("id_barang", "asc")->get();
        return view("admin.items.items", compact('barang'));
    }

    public function create()
    {
        return view('admin.items.create-items');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer',
        ]);

        $barang = Barang::where('nama', $validated['nama'])->first();

        if ($barang) {
            session()->flash('error', 'Items with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Barang::create([
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
        ]);

        if ($result) {
            session()->flash('success', 'Items created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('items-list')
            ]);
        } else {
            session()->flash('error', 'Failed to create item. Please try again.');

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
        $barang = Barang::where('id_barang', $id)->get();
        return view('admin.items.update-items', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer',
        ]);

        $barang = Barang::where('nama', $validated['nama'])->first();

        if ($barang) {
            session()->flash('error', 'Items with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Barang::where('id_barang', $id)->update([
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
        ]);

        if ($result) {
            session()->flash('success', 'Items updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('items-list')
            ]);
        } else {
            session()->flash('error', 'Failed to update item. Please try again.');

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
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('items-list')
            ->with('success', 'Items deleted successfully!');
    }
}
