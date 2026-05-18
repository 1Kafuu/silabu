<?php

namespace App\Services;

use App\Models\Poli;
use App\Models\Queue;
use Illuminate\Support\Facades\Cache;

class QueueService
{
    /**
     * Generate nomor antrian berikutnya untuk poli tertentu.
     * Contoh: A001, G002, AN003
     */
    public function generateQueueNumber(int $poliId): array
    {
        $poli = Poli::findOrFail($poliId);

        $lastQueue = Queue::where('poli_id', $poli->id)
            ->latest('queue_order')
            ->first();

        $next = $lastQueue
            ? $lastQueue->queue_order + 1
            : 1;

        return [
            'queue_order'  => $next,
            'queue_number' => $poli->code . str_pad($next, 3, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * Buat antrian baru untuk pasien.
     */
    public function createQueue(int $poliId, string $customerName): Queue
    {
        $generated = $this->generateQueueNumber($poliId);

        $queue = Queue::create([
            'poli_id'       => $poliId,
            'customer_name' => $customerName,
            'queue_number'  => $generated['queue_number'],
            'queue_order'   => $generated['queue_order'],
            'status'        => 'waiting',
        ]);

        $this->updateCache($poliId);

        return $queue;
    }

    /**
     * Panggil antrian waiting berikutnya berdasarkan urutan.
     * Return null jika tidak ada antrian waiting.
     */
    public function callNextQueue(int $poliId): ?Queue
    {
        $queue = Queue::where('poli_id', $poliId)
            ->where('status', 'waiting')
            ->orderBy('queue_order')
            ->first();

        if (!$queue) {
            return null;
        }

        $queue->update([
            'status'    => 'called',
            'called_at' => now(),
        ]);

        $this->updateCache($poliId);

        return $queue->fresh();
    }

    /**
     * Tandai antrian sebagai late (tidak hadir).
     */
    public function markQueueLate(Queue $queue): Queue
    {
        $queue->update([
            'status' => 'late',
        ]);

        $this->updateCache($queue->poli_id);

        return $queue->fresh();
    }

    /**
     * Panggil ulang antrian yang late.
     */
    public function recallLateQueue(Queue $queue): Queue
    {
        $queue->update([
            'status'    => 'called',
            'called_at' => now(),
        ]);

        $this->updateCache($queue->poli_id);

        return $queue->fresh();
    }

    /**
     * Ambil antrian yang sedang dipanggil (status called) untuk poli tertentu.
     */
    public function getCurrentQueue(int $poliId): ?Queue
    {
        return Queue::where('poli_id', $poliId)
            ->where('status', 'called')
            ->latest('called_at')
            ->first();
    }

    /**
     * Update cache state untuk SSE.
     * Menyimpan: current queue, waiting count, late count.
     */
    public function updateCache(int $poliId): void
    {
        $current = $this->getCurrentQueue($poliId);

        $waitingCount = Queue::where('poli_id', $poliId)
            ->where('status', 'waiting')
            ->count();

        $lateCount = Queue::where('poli_id', $poliId)
            ->where('status', 'late')
            ->count();

        Cache::put("queue_state_{$poliId}", [
            'current'       => $current ? [
                'id'            => $current->id,
                'queue_number'  => $current->queue_number,
                'customer_name' => $current->customer_name,
                'called_at'     => $current->called_at,
            ] : null,
            'waiting_count' => $waitingCount,
            'late_count'    => $lateCount,
            'updated_at'    => now()->toISOString(),
        ], now()->addHours(8));
    }

    /**
     * Ambil cache state untuk SSE stream.
     */
    public function getCacheState(int $poliId): array
    {
        return Cache::get("queue_state_{$poliId}", [
            'current'       => null,
            'waiting_count' => 0,
            'late_count'    => 0,
            'updated_at'    => now()->toISOString(),
        ]);
    }
}
