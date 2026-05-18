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
            ->whereDate('created_at', today())
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
            'status'     => 'called',
            'called_at'  => now(),
            'call_count' => 1,
        ]);

        $this->updateCache($poliId);

        return $queue->fresh();
    }

    /**
     * Panggil ulang antrian yang sedang dipanggil (max 3x).
     * Kalau sudah lebih dari 3x, tandai late dan panggil berikutnya.
     */
    public function recallCurrentQueue(Queue $queue): array
    {
        $newCount = $queue->call_count + 1;

        if ($newCount > 3) {
            // Sudah 3x dipanggil dan diklik lagi, tandai late
            $queue->update(['status' => 'late', 'call_count' => $newCount]);
            $this->updateCache($queue->poli_id);

            // Panggil berikutnya
            $next = $this->callNextQueue($queue->poli_id);

            return ['action' => 'late', 'next' => $next];
        }

        $queue->update([
            'call_count' => $newCount,
            'called_at'  => now(),
        ]);

        $this->updateCache($queue->poli_id);

        return ['action' => 'recalled', 'next' => $queue->fresh()];
    }

    /**
     * Tandai antrian sebagai selesai, lalu otomatis panggil berikutnya.
     */
    public function markQueueDone(Queue $queue): ?Queue
    {
        $queue->update([
            'status'      => 'done',
            'finished_at' => now(),
        ]);

        $this->updateCache($queue->poli_id);

        // Otomatis panggil antrian waiting berikutnya
        return $this->callNextQueue($queue->poli_id);
    }

    /**
     * Tandai antrian sebagai late (tidak hadir),
     * lalu otomatis panggil antrian waiting berikutnya jika ada.
     */
    public function markQueueLate(Queue $queue): Queue
    {
        $queue->update([
            'status' => 'late',
        ]);

        // Auto panggil berikutnya
        $this->callNextQueue($queue->poli_id);

        $this->updateCache($queue->poli_id);

        return $queue->fresh();
    }

    /**
     * Panggil ulang antrian yang late.
     */
    public function recallLateQueue(Queue $queue): Queue
    {
        $queue->update([
            'status'     => 'called',
            'called_at'  => now(),
            'call_count' => 1,
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
     * Menyimpan: current queue, waiting count, late count, waiting list, late list.
     */
    public function updateCache(int $poliId): void
    {
        $current = $this->getCurrentQueue($poliId);

        $waitingQueues = Queue::where('poli_id', $poliId)
            ->where('status', 'waiting')
            ->orderBy('queue_order')
            ->get(['id', 'queue_number', 'customer_name', 'queue_order']);

        $lateQueues = Queue::where('poli_id', $poliId)
            ->where('status', 'late')
            ->orderBy('queue_order')
            ->get(['id', 'queue_number', 'customer_name', 'queue_order']);

        $doneQueues = Queue::where('poli_id', $poliId)
            ->where('status', 'done')
            ->whereDate('created_at', today())
            ->orderBy('finished_at', 'desc')
            ->get(['id', 'queue_number', 'customer_name', 'finished_at']);

        Cache::put("queue_state_{$poliId}", [
            'current'       => $current ? [
                'id'            => $current->id,
                'queue_number'  => $current->queue_number,
                'customer_name' => $current->customer_name,
                'called_at'     => $current->called_at,
            ] : null,
            'waiting_count' => $waitingQueues->count(),
            'late_count'    => $lateQueues->count(),
            'done_count'    => $doneQueues->count(),
            'waiting_list'  => $waitingQueues->map(fn($q) => [
                'id'            => $q->id,
                'queue_number'  => $q->queue_number,
                'customer_name' => $q->customer_name,
            ])->values()->all(),
            'late_list'     => $lateQueues->map(fn($q) => [
                'id'            => $q->id,
                'queue_number'  => $q->queue_number,
                'customer_name' => $q->customer_name,
            ])->values()->all(),
            'done_list'     => $doneQueues->map(fn($q) => [
                'id'            => $q->id,
                'queue_number'  => $q->queue_number,
                'customer_name' => $q->customer_name,
                'finished_at'   => $q->finished_at,
            ])->values()->all(),
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
