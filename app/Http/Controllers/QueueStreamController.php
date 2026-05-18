<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QueueStreamController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    /**
     * SSE endpoint — stream state antrian realtime ke frontend.
     * Endpoint: GET /antrian/stream/{poli}
     */
    public function stream(Poli $poli): StreamedResponse
    {
        $poliId = $poli->id;

        return response()->stream(function () use ($poliId) {
            // Pastikan tidak ada output buffering yang menghambat flush
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            $lastState = null;

            while (true) {
                // Cek koneksi client masih aktif
                if (connection_aborted()) {
                    break;
                }

                $state = $this->queueService->getCacheState($poliId);

                // Kirim hanya jika ada perubahan state
                if ($state !== $lastState) {
                    $lastState = $state;

                    echo "data: " . json_encode($state) . "\n\n";

                    ob_flush();
                    flush();
                }

                // Kirim heartbeat setiap 30 detik agar koneksi tidak timeout
                echo ": heartbeat\n\n";
                ob_flush();
                flush();

                sleep(2);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable nginx buffering
            'Connection'        => 'keep-alive',
        ]);
    }
}
