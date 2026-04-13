<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use App\Traits\MidtransConfigTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    use MidtransConfigTrait;

    public function index(Request $request)
    {
        // Get all active vendors
        $vendors = Vendor::orderBy('nama_vendor', 'asc')->get();
        if (Auth::check()) {
            // Jika login, ambil berdasarkan id user tersebut
            $pesanan = Pesanan::with('user')
                ->where('iduser', Auth::id())
                ->orderBy('idpesanan', 'desc')
                ->get();
        } else {
            $pesanan = Pesanan::where('nama','like', 'GUEST-%') // Sesuaikan kolomnya, misal 'nama_pemesan'
                ->orderBy('idpesanan', 'desc')
                ->get();
        }

        // Get all menus from active vendors
        $menus = Menu::with('vendor')->orderBy('idmenu', 'asc')->get();

        if ($request->query('ajax') === 'transaksi') {
            return view('components.customer-transactions-table', compact('pesanan'));
        }

        return view('customer.dashboard', compact('vendors', 'menus', 'pesanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'total' => 'required|numeric',
            'metode_bayar' => 'required|string|max:255',
            'status_bayar' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.idmenu' => 'required|integer|exists:menu,idmenu',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        // Handle iduser - can be null for guest users
        if ($request->filled('iduser') && $request->input('iduser') != '') {
            $validated['iduser'] = $request->input('iduser');
            // Check if user is logged in but provided different iduser
            $user = auth()->user();
            if ($user && $user->id != $validated['iduser']) {
                // User is logged in but trying to submit for different user - use logged in user
                $validated['iduser'] = $user->id;
            }
        } else {
            $user = auth()->user();
            if ($user) {
                $validated['iduser'] = $user->id;
            } else {
                // Guest user - generate nama with GUEST-XXX format
                $lastPesanan = Pesanan::where('nama', 'like', 'GUEST-%')->orderBy('idpesanan', 'desc')->first();
                if ($lastPesanan) {
                    // Extract number from GUEST-XXX
                    $lastNumber = (int) substr($lastPesanan->nama, 6);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }
                $validated['nama'] = 'GUEST-' . sprintf('%03d', $nextNumber);
                $validated['iduser'] = null;
            }
        }

        $pesanan = Pesanan::where('nama', $validated['nama'])->first();

        if ($pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan dengan nama yang sama sudah ada. Silakan coba lagi.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $result = Pesanan::create($validated);

            foreach ($request->input('items', []) as $item) {
                DB::table('detail_pesanan')->insert([
                    'idmenu' => $item['idmenu'],
                    'idpesanan' => $result->idpesanan,
                    'jumlah' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                    'timestamp' => now(),
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to save pesanan detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pesanan. Silakan coba lagi.'
            ], 500);
        }

        $this->configureMidtrans();

        // Create Midtrans Transaction
        $params = [
            'transaction_details' => [
                'order_id' => $result->idpesanan,
                'gross_amount' => $validated['total'],
            ],
            'customer_details' => [
                'first_name' => $validated['nama'],
                'email' => 'guest@pesanan.com',
                'phone' => '081234567890',
            ],
        ];

        if ($result->iduser) {
            $user = User::find($result->iduser);
            if ($user) {
                $params['customer_details']['first_name'] = $user->name;
                $params['customer_details']['email'] = $user->email;
            }
        }

        try {
            $midtransResponse = \Midtrans\snap::createTransaction($params);
            $result->snap_token = $midtransResponse->token;
            $result->save();
        } catch (\Exception $e) {
            \Log::error('Midtrans transaction error: ' . $e->getMessage());
        }

        if ($result) {
            // Get the updated pesanan with midtrans token
            $result->loadMissing('user');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'redirect' => route('customer-list'),
                'snap_token' => $result->snap_token,
                'order_id' => $result->idpesanan
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pesanan. Silakan coba lagi.'
            ], 500);
        }
    }
}
