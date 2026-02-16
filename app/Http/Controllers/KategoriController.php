<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index() {
        $kategori = Kategori::whereNull('deleted_at')->orderBy('idkategori', 'asc')->get();
        return view('admin.category.category', compact('kategori'));
    }

    public function create() {
        return view('admin.category.create-category');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::where('nama_kategori', $validated['nama_kategori'])->first();

        if ($kategori) {
            return redirect()->back()->withInput()->with('error', 'Kategori dengan nama yang sama sudah ada');
        }

        $result = Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return redirect()->route('category-list')->with('success','Category created successfully!');
    }

    public function edit($id) {
        $kategori = Kategori::where('idkategori', $id)->get();
        return view('admin.category.update-category', compact('kategori'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = Kategori::where('nama_kategori', $validated['nama_kategori'])->first();

        if ($kategori) {
            return redirect()->back()->withInput()->with('error', 'Category with the same name already exists');
        }

        $result = Kategori::where('idkategori', $id)->update([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        return redirect()->route('category-list')->with('success','Category updated successfully!');
    }

    public function delete($id)
    {
        $user = Kategori::findOrFail($id); 
        $user->delete();

        return redirect()->route('category-list')->with('success', 'Category deleted successfully!');
    }
}
