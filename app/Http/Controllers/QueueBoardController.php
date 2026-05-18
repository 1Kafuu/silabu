<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use App\Services\QueueService;

class QueueBoardController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    /**
     * Papan antrian realtime per poli.
     */
    public function index(Poli $poli)
    {
        $poli->load('queues');

        $state = $this->queueService->getCacheState($poli->id);

        return view('queue.board', compact('poli', 'state'));
    }
}
