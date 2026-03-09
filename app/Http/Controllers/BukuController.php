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

        if ($buku) {
            session()->flash('error', 'Book with the same name or code already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Buku::create([
            'kode' => $validated['kode'],
            'judul' => $validated['judul'],
            'pengarang' => $validated['pengarang'],
            'idkategori' => $validated['idkategori'],
        ]);

        if ($result) {
            session()->flash('success', 'Book created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('book-list')
            ]);
        } else {
            session()->flash('error', 'Failed to create book. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
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

        if ($result) {
            session()->flash('success', 'Book updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('book-list')
            ]);
        } else {
            session()->flash('error', 'Failed to update book. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function delete($id) {
        $buku = Buku::findOrFail($id); 
        $buku->delete();

        return redirect()->route('book-list')->with('success', 'Book deleted successfully!');
    }
}
