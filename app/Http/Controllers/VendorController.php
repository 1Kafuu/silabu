<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{

    public function index()
    {
        $vendors = Vendor::orderBy('idvendor', 'asc')->get();
        return view('admin.vendor.vendor', compact('vendors'));
    }

    public function create()
    {
        $users = User::whereHas('role_user', function ($query) {
            $query->where('idrole', 3)->where('status', 1); // Mencari ID 3 di tabel role_user
        })->get();
        return view('admin.vendor.create-vendor', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'iduser' => 'required|exists:users,id',
        ]);

        $vendor = Vendor::where('nama_vendor', $validated['nama_vendor'])->first();

        if ($vendor) {
            session()->flash('error', 'Vendor with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Vendor::create($validated);

        if ($result) {
            session()->flash('success', 'Vendor created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('vendor-list')
            ]);
        } else {
            session()->flash('error', 'Failed to create vendor. Please try again.');

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
        $vendor = Vendor::findOrFail($id);
        $users = User::whereHas('role_user', function ($query) {
            $query->where('idrole', 3)->where('status', 1);
        })->get();

        return view('admin.vendor.update-vendor', compact('vendor', 'users'));
    }

    public function update($id, Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:255',
            'iduser' => 'required|exists:users,id',
        ]);

        $vendor = Vendor::where('idvendor', $id)->update($validated);

        if ($vendor) {
            session()->flash('success', 'Vendor updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('vendor-list')
            ]);
        } else {
            session()->flash('error', 'Failed to update vendor. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }

    public function menu()
    {
        $menus = Menu::whereHas('vendor', function ($query) {
            $query->where('iduser', Auth::id());
        })->with('vendor.user')->get();
        return view('vendor.dashboard', compact('menus'));
    }

    public function pesanan()
    {
        $idvendor = auth()->user()->vendor->idvendor ?? null;
        if (!$idvendor) {
            abort(403, 'Vendor tidak ditemukan untuk user ini.');
        }

        $orders = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.idmenu', '=', 'menu.idmenu')
            ->join('pesanan', 'detail_pesanan.idpesanan', '=', 'pesanan.idpesanan')
            ->where('menu.idvendor', $idvendor)
            ->whereIn('pesanan.status_bayar', ['success', 'paid'])
            ->select(
                'pesanan.idpesanan',
                'pesanan.nama as nama_pemesan',
                'pesanan.metode_bayar',
                'pesanan.status_bayar',
                'detail_pesanan.jumlah',
                'detail_pesanan.harga',
                'detail_pesanan.subtotal',
                'detail_pesanan.catatan',
                'detail_pesanan.timestamp',
                'menu.nama_menu'
            )
            ->orderBy('detail_pesanan.timestamp', 'desc')
            ->get();

        return view('vendor.pesanan', compact('orders'));
    }

    public function scanQr()
    {
        return view('vendor.scan_qr');
    }

    public function findByQRcode($id)
    {
        $orders = DB::table('detail_pesanan')
            ->join('menu', 'detail_pesanan.idmenu', '=', 'menu.idmenu')
            ->join('pesanan', 'detail_pesanan.idpesanan', '=', 'pesanan.idpesanan')
            ->where('detail_pesanan.idpesanan', $id)
            ->select(
                'pesanan.idpesanan',
                'pesanan.nama as nama_pemesan',
                'pesanan.total',
                'pesanan.metode_bayar',
                'pesanan.status_bayar',
                'detail_pesanan.jumlah',
                'detail_pesanan.harga',
                'detail_pesanan.subtotal',
                'detail_pesanan.catatan',
                'detail_pesanan.timestamp',
                'menu.nama_menu'
            )
            ->orderBy('detail_pesanan.timestamp', 'asc')
            ->get();

        $pesanan = DB::table('pesanan')
            ->where('idpesanan', $id)
            ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        $statusMap = [
            'pending' => ['label' => 'Pending', 'badge' => 'bg-warning text-dark'],
            'success' => ['label' => 'Success', 'badge' => 'bg-success'],
            'paid' => ['label' => 'Paid', 'badge' => 'bg-success'],
            'failed' => ['label' => 'Failed', 'badge' => 'bg-danger'],
            'cancelled' => ['label' => 'Cancelled', 'badge' => 'bg-secondary'],
        ];

        $status = $statusMap[$pesanan->status_bayar] ?? [
            'label' => ucfirst((string) $pesanan->status_bayar),
            'badge' => 'bg-secondary'
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $pesanan->idpesanan,
                'customer_name' => $pesanan->nama,
                'total' => (float) $pesanan->total,
                'total_formatted' => 'Rp ' . number_format((float) $pesanan->total, 0, ',', '.'),
                'metode_bayar' => $pesanan->metode_bayar,
                'status_bayar' => $pesanan->status_bayar,
                'status_label' => $status['label'],
                'status_badge' => $status['badge'],
                'items' => $orders->map(function ($item) {
                    return [
                        'nama_menu' => $item->nama_menu,
                        'qty' => (int) $item->jumlah,
                        'harga' => (float) $item->harga,
                        'harga_formatted' => 'Rp ' . number_format((float) $item->harga, 0, ',', '.'),
                        'subtotal' => (float) $item->subtotal,
                        'subtotal_formatted' => 'Rp ' . number_format((float) $item->subtotal, 0, ',', '.'),
                        'catatan' => $item->catatan,
                        'timestamp' => $item->timestamp,
                    ];
                })->values(),
                'scanned_at' => now()->format('d M Y H:i:s'),
            ]
        ]);
    }

    public function createMenu()
    {
        return view('vendor.create-menu');
    }

    public function editMenu($id)
    {
        $menu = Menu::findOrFail($id);
        return view('vendor.update-menu', compact('menu'));
    }

    public function updateMenu($id, Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $menu = Menu::findOrFail($id);

        // Handle image upload if new image is provided
        if ($request->hasFile('path_gambar')) {
            //   old image if exists
            if ($menu->path_gambar && file_exists(storage_path('app/public/' . $menu->path_gambar))) {
                unlink(storage_path('app/public/' . $menu->path_gambar));
            }

            // Store new image
            $path = $request->file('path_gambar')->store('menus', 'public');
            $validated['path_gambar'] = $path;
        }

        $menu->update($validated);

        if ($menu) {
            session()->flash('success', 'Menu updated successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('menu-list')
            ]);
        } else {
            session()->flash('error', 'Failed to update menu. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }


    public function deleteMenu($id)
    {
        $menu = Menu::findOrFail($id);

        // Delete image from storage if exists
        if ($menu->path_gambar && file_exists(storage_path('app/public/' . $menu->path_gambar))) {
            unlink(storage_path('app/public/' . $menu->path_gambar));
        }

        $menu->delete();

        return redirect()->route('menu-list')->with('success', 'Menu deleted successfully!');
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'path_gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $idvendor = auth()->user()->vendor->idvendor ?? null;
        \Log::info('ID Vendor: ' . $idvendor);

        if (!$idvendor) {
            session()->flash('error', 'User does not have an associated vendor.');

            $notificationHTML = view('components.notification')->render();

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 403);
        }

        $menu = Menu::where('nama_menu', $validated['nama_menu'])->where('idvendor', $idvendor)->first();

        if ($menu) {
            session()->flash('error', 'Menu with the same name already exists for this vendor. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        // Upload gambar
        $path = $request->file('path_gambar')->store('menus', 'public');
        $validated['path_gambar'] = $path;
        $validated['idvendor'] = $idvendor;

        $result = Menu::create($validated);

        if ($result) {
            session()->flash('success', 'Menu created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('menu-list')
            ]);
        } else {
            session()->flash('error', 'Failed to create menu. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }
}
