<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index() {
        $book = Buku::whereNull('deleted_at')->with('category')->get();
        return view('admin.book.book', compact('book'));
    }

    public function create() {
        $kategori = Kategori::whereNull('deleted_at')->get();
        return view('admin.book.create-book', compact('kategori'));
    }

    public function store(Request $request) {
        // dd(request()->all());
        $validated = $request->validate([
            'kode' => 'required|string|max:255',
            'judul'=> 'required|string|max:255',
            'pengarang'=> 'required|string|max:255',
            'idkategori'=> 'required|integer',
        ]);

        $buku = Buku::where('kode', $validated['kode'])->orWhere('judul', $validated['judul'])->first();

        if($buku) {
            return redirect()->back()->withInput()->with('error','Kode atau Judul buku yang sama sudah ada');
        }

        $result = Buku::create([
            'kode' => $validated['kode'],
            'judul' => $validated['judul'],
            'pengarang' => $validated['pengarang'],
            'idkategori' => $validated['idkategori'],
        ]);

        return redirect()->route('book-list')->with('success','Book created successfully!');
    }
    
    public function edit($id) {
        $buku = Buku::where('idbuku', $id)->with('category')->get();
        $kategori = Kategori::whereNull('deleted_at')->get();
        return view('admin.book.update-book', compact('kategori', 'buku'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'kode' => 'required|string|max:255',
            'judul'=> 'required|string|max:255',
            'pengarang'=> 'required|string|max:255',
            'idkategori'=> 'required|integer',
        ]);

        $result = Buku::where('idbuku', $id)->update([
            'kode' => $validated['kode'],
            'judul'=> $validated['judul'],
            'pengarang'=> $validated['pengarang'],
            'idkategori'=> $validated['idkategori'],
        ]);

        return redirect()->route('book-list')->with('success','Book updatede successfully!');
    }

    public function delete($id) {
        $buku = Buku::findOrFail($id); 
        $buku->delete();

        return redirect()->route('book-list')->with('success', 'Book deleted successfully!');
    }
}
