<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Get all active vendors
        $vendors = Vendor::orderBy('nama_vendor', 'asc')->get();
        $pesanan = Pesanan::with('user')->orderBy('idpesanan', 'asc')->get();
        
        // Get all menus from active vendors
        $menus = Menu::with('vendor')->orderBy('idmenu', 'asc')->get();
        
        return view('customer.dashboard', compact('vendors', 'menus', 'pesanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'total' => 'required|numeric',
            'metode_bayar' => 'required|string|max:255',
            'status_bayar' => 'required|string|max:255',
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
                    $lastNumber = (int)substr($lastPesanan->nama, 6);
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
            session()->flash('error', 'Pesanan with the same name already exists. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }

        $result = Pesanan::create($validated);

        // Midtrans Configuration
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config:: $isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

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
            session()->flash('success', 'Pesanan created successfully!');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            // Get the updated pesanan with midtrans token
            $result->loadMissing('user');
            
            return response()->json([
                'success' => true,
                'notification' => $notificationHTML,
                'redirect' => route('customer-list'),
                'snap_token' => $result->snap_token,
                'order_id' => $result->idpesanan
            ]);
        } else {
            session()->flash('error', 'Failed to create pesanan. Please try again.');

            $notificationHTML = view('components.notification')->render();

            \Log::info('Notification HTML: ' . $notificationHTML);

            return response()->json([
                'success' => false,
                'notification' => $notificationHTML
            ], 500);
        }
    }
}
