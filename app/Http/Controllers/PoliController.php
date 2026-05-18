<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        $polis = Poli::all();
        return view('admin.poli.poli', compact('polis'));
    }

    public function create()
    {
        return view('admin.poli.create-poli');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:poli,code',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $existing = Poli::where('name', $validated['name'])->first();

        if ($existing) {
            session()->flash('error', 'Poli dengan nama yang sama sudah ada. Silakan coba lagi.');

            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML,
            ], 500);
        }

        $result = Poli::create($validated);

        if ($result) {
            session()->flash('success', 'Poli berhasil ditambahkan!');

            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('poli'),
            ]);
        }

        session()->flash('error', 'Gagal menambahkan poli. Silakan coba lagi.');

        $notificationHTML = view('components.notification')->render();

        return response()->json([
            'success' => false,
            'notification' => $notificationHTML,
        ], 500);
    }

    public function edit($id)
    {
        $poli = Poli::findOrFail($id);
        return view('admin.poli.update-poli', compact('poli'));
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'required|string|max:10|unique:poli,code,' . $id,
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $result = Poli::where('id', $id)->update($validated);

        if ($result !== false) {
            session()->flash('success', 'Poli berhasil diperbarui!');

            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('poli'),
            ]);
        }

        session()->flash('error', 'Gagal memperbarui poli. Silakan coba lagi.');

        $notificationHTML = view('components.notification')->render();

        return response()->json([
            'success' => false,
            'notification' => $notificationHTML,
        ], 500);
    }

    public function delete($id)
    {
        $poli = Poli::findOrFail($id);
        $poli->delete();

        return redirect()->route('poli')->with('success', 'Poli berhasil dihapus!');
    }
}
