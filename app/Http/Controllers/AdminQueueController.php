<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Models\Queue;
use App\Services\QueueService;

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
            ->whereHas('role', fn($q) => $q->where('nama_role', 'Admin Loket'))
            ->where(function ($q) use ($poli) {
                $q->where('poli_id', $poli->id)
                  ->orWhereNull('poli_id');
            })
            ->where('status', '1')
            ->exists();

        abort_if(!$hasAccess, 403, 'Anda tidak memiliki akses ke poli ini.');
    }

    /**
     * Dashboard admin per poli — tampil waiting, late, done, current.
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

        $doneQueues = Queue::where('poli_id', $poli->id)
            ->where('status', 'done')
            ->whereDate('created_at', today())
            ->orderBy('finished_at', 'desc')
            ->get();

        $polis = Poli::where('is_active', true)->get();

        return view('queue.admin.index', compact(
            'poli',
            'current',
            'waitingQueues',
            'lateQueues',
            'doneQueues',
            'polis'
        ));
    }

    /**
     * Panggil antrian pertama jika belum ada yang dipanggil,
     * atau panggil ulang antrian saat ini (max 3x lalu auto-late).
     */
    public function callNext(Poli $poli)
    {
        $this->authorizePoliAccess($poli);

        $current = $this->queueService->getCurrentQueue($poli->id);

        if ($current) {
            // Ada yang sedang dipanggil — panggil ulang
            $result = $this->queueService->recallCurrentQueue($current);

            if ($result['action'] === 'late') {
                $msg = "Antrian {$current->queue_number} ditandai late setelah 3x panggilan.";
                if ($result['next']) {
                    $msg .= " Memanggil {$result['next']->queue_number} — {$result['next']->customer_name}.";
                }
            } else {
                $q   = $result['next'];
                $msg = "Memanggil ulang {$q->queue_number} — {$q->customer_name} (panggilan ke-{$q->call_count}).";
            }

            return back()->with('success', $msg);
        }

        // Belum ada yang dipanggil — panggil antrian pertama
        $queue = $this->queueService->callNextQueue($poli->id);

        if (!$queue) {
            return back()->with('info', 'Tidak ada antrian waiting.');
        }

        return back()->with('success', "Memanggil {$queue->queue_number} — {$queue->customer_name}.");
    }

    /**
     * Tandai antrian sebagai selesai, otomatis panggil berikutnya.
     */
    public function markDone(Queue $queue)
    {
        $this->authorizePoliAccess($queue->poli);

        $next = $this->queueService->markQueueDone($queue);

        $msg = "Antrian {$queue->queue_number} selesai.";
        if ($next) {
            $msg .= " Memanggil {$next->queue_number} — {$next->customer_name}.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Tandai antrian sebagai late, otomatis panggil berikutnya.
     */
    public function markLate(Queue $queue)
    {
        $this->authorizePoliAccess($queue->poli);

        $this->queueService->markQueueLate($queue);

        $next = $this->queueService->getCurrentQueue($queue->poli_id);
        $msg  = "Antrian {$queue->queue_number} ditandai late.";
        if ($next) {
            $msg .= " Memanggil {$next->queue_number} — {$next->customer_name}.";
        }

        return back()->with('success', $msg);
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
