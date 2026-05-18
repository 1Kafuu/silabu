<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\Request;

class AdminQueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    /**
     * Cek apakah user yang login punya akses ke poli ini.
     */
    private function authorizePoliAccess(Poli $poli): void
    {
        $user = auth()->user();

        $hasAccess = $user->role_user()
            ->whereHas('role', fn($q) => $q->where('nama_role', 'AdminLoket'))
            ->where(function ($q) use ($poli) {
                $q->where('poli_id', $poli->id)
                  ->orWhereNull('poli_id'); // null = akses semua poli
            })
            ->where('status', 'active')
            ->exists();

        abort_if(!$hasAccess, 403, 'Anda tidak memiliki akses ke poli ini.');
    }

    /**
     * Dashboard admin per poli — tampil waiting, late, current.
     */
    public function index(Poli $poli)
    {
        $this->authorizePoliAccess($poli);

        $current = $this->queueService->getCurrentQueue($poli->id);

        $waitingQueues = Queue::where('poli_id', $poli->id)
            ->where('status', 'waiting')
            ->orderBy('queue_order')
            ->get();

        $lateQueues = Queue::where('poli_id', $poli->id)
            ->where('status', 'late')
            ->orderBy('queue_order')
            ->get();

        $polis = Poli::where('is_active', true)->get();

        return view('queue.admin', compact(
            'poli',
            'current',
            'waitingQueues',
            'lateQueues',
            'polis'
        ));
    }

    /**
     * Panggil antrian waiting berikutnya.
     */
    public function callNext(Poli $poli)
    {
        $this->authorizePoliAccess($poli);

        $queue = $this->queueService->callNextQueue($poli->id);

        if (!$queue) {
            return back()->with('info', 'Tidak ada antrian waiting.');
        }

        return back()->with('success', "Memanggil {$queue->queue_number} — {$queue->customer_name}");
    }

    /**
     * Tandai antrian sebagai late.
     */
    public function markLate(Queue $queue)
    {
        $this->authorizePoliAccess($queue->poli);

        $this->queueService->markQueueLate($queue);

        return back()->with('success', "Antrian {$queue->queue_number} ditandai late.");
    }

    /**
     * Panggil ulang antrian late.
     */
    public function callLate(Queue $queue)
    {
        $this->authorizePoliAccess($queue->poli);

        $this->queueService->recallLateQueue($queue);

        return back()->with('success', "Memanggil ulang {$queue->queue_number} — {$queue->customer_name}");
    }
}
