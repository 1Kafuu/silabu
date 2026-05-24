<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Antrian - {{ $poli->name }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/queue/board.css') }}">

</head>
<body>

    {{-- Header --}}
    <div class="board-header">
        <div class="hospital">
            <div class="hospital-icon"><span class="mdi mdi-hospital-building"></span></div>
            <div>
                <h1>Papan Antrian</h1>
                <div class="poli-tag">{{ $poli->name }}</div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:14px;">
            <button id="sound-btn" title="Aktifkan Suara">
                <span class="mdi mdi-volume-off" id="sound-icon"></span>
            </button>
            <div class="clock" id="clock">--:--:--</div>
        </div>
    </div>

    {{-- Main --}}
    <div class="board-main">

        {{-- Current Queue --}}
        <div class="current-panel">
            <div class="now-serving">Nomor yang Dipanggil</div>

            @if ($state['current'])
                <div class="queue-number" id="board-number">{{ $state['current']['queue_number'] }}</div>
                <div class="patient-name" id="board-name">{{ $state['current']['customer_name'] }}</div>
            @else
                <div class="queue-number" id="board-number" style="display:none;"></div>
                <div class="patient-name" id="board-name" style="display:none;"></div>
                <div class="no-queue" id="no-queue-msg">
                    <span class="mdi mdi-timer-sand"></span>
                    Menunggu antrian dipanggil...
                </div>
            @endif

            <div class="poli-label">{{ $poli->name }}</div>
        </div>

        {{-- Right Panel --}}
        <div class="right-panel">
            <div class="stats-panel">
                <div class="stat-box">
                    <div class="s-num waiting" id="board-waiting">{{ $state['waiting_count'] }}</div>
                    <div class="s-lbl">Menunggu</div>
                </div>
                <div class="stat-box">
                    <div class="s-num late" id="board-late">{{ $state['late_count'] }}</div>
                    <div class="s-lbl">Terlambat</div>
                </div>
            </div>

            <div class="queue-list-panel">
                <h3 id="queue-list-header">Antrian Menunggu ({{ count($state['waiting_list'] ?? []) }})</h3>
                <div id="queue-list">
                    @forelse ($state['waiting_list'] ?? [] as $i => $q)
                        <div class="queue-list-item">
                            <span class="q-num">{{ $q['queue_number'] }}</span>
                            <span class="q-name">{{ $q['customer_name'] }}</span>
                            <span class="q-order">#{{ $i + 1 }}</span>
                        </div>
                    @empty
                        <div class="queue-list-empty">
                            <span class="mdi mdi-check-circle-outline"></span>
                            Tidak ada antrian menunggu
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="board-footer">
        <div class="status">
            <div class="dot" id="conn-dot"></div>
            <span id="conn-label">Terhubung</span>
        </div>
        <div class="updated" id="last-updated">—</div>
    </div>

    <audio id="audio-dingdong" preload="auto">
        <source src="{{ asset('music/hospital-announcement.mp3') }}" type="audio/mpeg">
    </audio>

    <script>
        // ── Clock ──────────────────────────────────────────────
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent =
                now.toLocaleTimeString('id-ID', { hour12: false });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ── Audio unlock via sound button ──────────────────────
        let audioUnlocked = false;

        function unlockAudio() {
            if (audioUnlocked) return;
            audioUnlocked = true;
            const audio = document.getElementById('audio-dingdong');
            audio.volume = 0;
            audio.play().then(() => {
                audio.pause();
                audio.currentTime = 0;
                audio.volume = 1;
            }).catch(() => {});
            if ('speechSynthesis' in window) {
                const warmup = new SpeechSynthesisUtterance('');
                warmup.volume = 0;
                window.speechSynthesis.speak(warmup);
            }
            const btn  = document.getElementById('sound-btn');
            const icon = document.getElementById('sound-icon');
            if (btn)  { btn.classList.add('active'); btn.title = 'Suara Aktif'; }
            if (icon) icon.className = 'mdi mdi-volume-high';
        }

        document.getElementById('sound-btn').addEventListener('click', function () {
            unlockAudio();
            if (audioUnlocked) {
                const audio = document.getElementById('audio-dingdong');
                audio.currentTime = 0;
                audio.play().catch(() => {});
            }
        });

        // ── SSE Config ─────────────────────────────────────────
        window.QueueSSEConfig = {
            streamUrl : '{{ route('queue-stream', $poli->id) }}',
            mode      : 'board',
            poliName  : @json($poli->name),
            lastQueue : @json($state['current']['queue_number'] ?? null),
        };
    </script>
    <script src="{{ asset('js/queue-sse.js') }}"></script>
</body>
</html>
