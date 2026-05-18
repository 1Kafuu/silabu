<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;

class PoliAssignController extends Controller
{
    /**
     * Halaman assign admin loket ke poli.
     */
    public function index()
    {
        $polis = Poli::where('is_active', true)->get();
        $users = User::all();

        // Role Admin Loket
        $roleAdminLoket = Role::where('nama_role', 'Admin Loket')->first();

        // Ambil semua assignment Admin Loket yang sudah ada
        $assignments = RoleUser::with(['user', 'poli'])
            ->when($roleAdminLoket, fn($q) => $q->where('idrole', $roleAdminLoket->idrole))
            ->get();

        return view('admin.poli.assign-loket', compact('polis', 'users', 'roleAdminLoket', 'assignments'));
    }

    /**
     * Simpan assignment admin loket ke multiple poli.
     */
    public function store(Request $request)
    {
        $request->validate([
            'iduser'    => 'required|exists:users,id',
            'poli_ids'  => 'required|array|min:1',
            'poli_ids.*'=> 'required|exists:poli,id',
        ]);

        $roleAdminLoket = Role::where('nama_role', 'Admin Loket')->firstOrFail();

        $added    = 0;
        $skipped  = 0;

        foreach ($request->poli_ids as $poliId) {
            $existing = RoleUser::where('iduser', $request->iduser)
                ->where('idrole', $roleAdminLoket->idrole)
                ->where('poli_id', $poliId)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            RoleUser::create([
                'iduser'  => $request->iduser,
                'idrole'  => $roleAdminLoket->idrole,
                'poli_id' => $poliId,
                'status'  => '1',
            ]);
            $added++;
        }

        if ($added === 0) {
            session()->flash('error', 'Semua poli yang dipilih sudah di-assign untuk user ini.');
            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success'      => false,
                'notification' => $notificationHTML,
            ], 422);
        }

        $msg = "Berhasil assign {$added} poli.";
        if ($skipped > 0) {
            $msg .= " {$skipped} poli dilewati (sudah ada).";
        }

        session()->flash('success', $msg);
        $notificationHTML = view('components.notification')->render();

        return response()->json([
            'success'      => true,
            'notification' => $notificationHTML,
            'redirect'     => route('poli.assign-loket'),
        ]);
    }

    /**
     * Hapus assignment admin loket.
     */
    public function destroy($id)
    {
        $assignment = RoleUser::findOrFail($id);
        $assignment->delete();

        return redirect()->route('poli.assign-loket')->with('success', 'Assignment berhasil dihapus!');
    }
}
