<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::orderBy("id_barang","asc")->get();
        return view("admin.items.items",compact('barang') );
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
            return redirect()->back()->withInput()
            ->with('error', 'Items with the same name already exists');
        }

        $result = Barang::create([
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
        ]);

        return redirect()->route('items-list')
        ->with('success','Items created successfully!');
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

        $result = Barang::where('id_barang', $id)->update([
            'nama' => $validated['nama'],
            'harga' => $validated['harga'],
        ]);

        return redirect()->route('items-list')
        ->with('success','Items updated successfully!');
    }

    public function delete($id)
    {
        $barang = Barang::findOrFail($id); 
        $barang->delete();

        return redirect()->route('items-list')
        ->with('success', 'Items deleted successfully!');
    }
}
