<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\Request;

class GuestQueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    /**
     * Halaman form ambil nomor antrian.
     */
    public function index()
    {
        $polis = Poli::where('is_active', true)->get();

        return view('queue.guest', compact('polis'));
    }

    /**
     * Simpan antrian baru dan redirect ke tiket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'poli_id'       => 'required|exists:poli,id',
        ]);

        $queue = $this->queueService->createQueue(
            $request->poli_id,
            $request->customer_name
        );

        return redirect()->route('queue.ticket', $queue->id);
    }

    /**
     * Halaman tiket antrian pasien.
     */
    public function ticket(Queue $queue)
    {
        $queue->load('poli');

        return view('queue.ticket', compact('queue'));
    }
}
