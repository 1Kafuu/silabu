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
            session()->flash('error', 'Category with the same name. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Kategori::create([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        if ($result) {
            session()->flash('success', 'Category created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('category-list')
            ]);
        } else {
            session()->flash('error', 'Failed to create category. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

    }

    public function edit($id) {
        $kategori = Kategori::where('idkategori', $id)->get();
        return view('admin.category.update-category', compact('kategori'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);


        $result = Kategori::where('idkategori', $id)->update([
            'nama_kategori' => $validated['nama_kategori'],
        ]);

        if ($result) {
            session()->flash('success', 'Category updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('category-list')
            ]);
        } else {
            session()->flash('error', 'Failed to update category. Please try again.');

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
        $user = Kategori::findOrFail($id); 
        $user->delete();

        return redirect()->route('category-list')
        ->with('success', 'Category deleted successfully!');
    }
}
