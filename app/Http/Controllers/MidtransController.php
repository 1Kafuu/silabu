<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Traits\MidtransConfigTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransController extends Controller
{
    use MidtransConfigTrait;

    public function callback(Request $request): Response
    {
        $this->configureMidtrans();

        // Get notification body
        $notificationBody = file_get_contents('php://input');
        $notificationSignature = $request->header('X-Signature');

        // Verify signature if available
        if ($notificationSignature) {
            $signatureKey = hash('sha512', config('midtrans.server_key') . $notificationBody);
            if ($signatureKey !== $notificationSignature) {
                \Log::warning('Midtrans Callback: Invalid signature');
                return response('Invalid signature', 403);
            }
        }

        // Parse notification body
        $notificationObj = json_decode($notificationBody);

        // Get transaction information
        $orderId = $notificationObj->order_id ?? null;
        $transactionStatus = $notificationObj->transaction_status ?? null;
        $paymentType = $notificationObj->payment_type ?? null;
        $fraudStatus = $notificationObj->fraud_status ?? null;
        $statusMessage = $notificationObj->status_message ?? null;

        // Log the callback for debugging
        \Log::info('Midtrans Callback Received', [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'fraud_status' => $fraudStatus,
            'status_code' => $notificationObj->status_code ?? null,
            'status_message' => $statusMessage
        ]);

        // Find the order
        $pesanan = Pesanan::where('idpesanan', $orderId)->first();

        if (!$pesanan) {
            \Log::warning('Midtrans Callback: Order not found - ' . $orderId);
            return response('Order not found', 404);
        }

        // Update payment status based on transaction status
        switch ($transactionStatus) {
            case 'capture':
                // For credit card payments
                if ($fraudStatus === 'challenge') {
                    $pesanan->status_bayar = 'pending';
                    $pesanan->metode_bayar = 'midtrans';
                } elseif ($fraudStatus === 'accept') {
                    $pesanan->status_bayar = 'success';
                    $pesanan->metode_bayar = $paymentType ?? 'midtrans';
                }
                break;
            case 'settlement':
                // Payment successfully settled - update metode_bayar from Midtrans payment_type
                $pesanan->status_bayar = 'success';
                $pesanan->metode_bayar = $paymentType ?? 'midtrans';
                break;
            case 'pending':
                // Payment pending (waiting for customer to settle) - keep as midtrans dummy
                $pesanan->status_bayar = 'pending';
                $pesanan->metode_bayar = 'midtrans';
                break;
            case 'deny':
                // Payment denied
                $pesanan->status_bayar = 'failed';
                $pesanan->metode_bayar = $paymentType ?? 'midtrans';
                break;
            case 'cancel':
            case 'expire':
                // Payment cancelled or expired
                $pesanan->status_bayar = 'cancelled';
                $pesanan->metode_bayar = $paymentType ?? 'midtrans';
                break;
        }

        // Save the updated payment status
        $pesanan->save();

        // Additional processing for successful payment
        if ($pesanan->status_bayar === 'success') {
            \Log::info('Payment successful for order: ' . $orderId);
        }

        // Return success response to Midtrans
        return response('OK', 200);
    }

    public function payPending(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:pesanan,idpesanan',
        ]);

        $pesanan = Pesanan::with('user')
            ->where('idpesanan', $request->order_id)
            ->where('status_bayar', 'pending')
            ->first();

        if (!$pesanan) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan atau bukan status pending.'
            ], 404);
        }

        // Reuse an existing snap token if the order already has one.
        // This avoids Midtrans 400 error when order_id was already registered.
        if ($pesanan->snap_token) {
            return response()->json([
                'success' => true,
                'snap_token' => $pesanan->snap_token,
                'order_id' => $pesanan->idpesanan,
                'message' => 'Existing snap token returned.'
            ]);
        }

        $this->configureMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $pesanan->idpesanan,
                'gross_amount' => $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => optional($pesanan->user)->name ?? $pesanan->nama,
                'email' => optional($pesanan->user)->email ?? 'guest@pesanan.com',
                'phone' => '081234567890',
            ],
        ];

        try {
            $midtransResponse = \Midtrans\snap::createTransaction($params);
            $pesanan->snap_token = $midtransResponse->token;
            $pesanan->save();

            return response()->json([
                'success' => true,
                'snap_token' => $pesanan->snap_token,
                'order_id' => $pesanan->idpesanan
            ]);
        } catch (\Exception $e) {
            \Log::error('Midtrans pay pending error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran Midtrans.'
            ], 500);
        }
    }
}
