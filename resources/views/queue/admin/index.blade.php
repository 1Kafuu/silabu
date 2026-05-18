<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - {{ $poli->name }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/queue/admin.css') }}">
</head>
<body>

    <div class="page-header">
        <div class="brand">
            <div class="brand-icon"><span class="mdi mdi-monitor-dashboard"></span></div>
            <div>
                <h1>Dashboard Admin Antrian</h1>
                <div class="sub">{{ $poli->name }}</div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <a href="{{ route('queue-board', $poli->id) }}"
               target="_blank"
               class="board-btn">
                <span class="mdi mdi-television-play"></span>
                Buka Papan Antrian
            </a>
            <div class="realtime-pill">
                <div class="realtime-dot" id="conn-dot"></div>
                <span id="conn-label">Realtime</span>
                <span id="sse-countdown" style="opacity:0.6; font-size:0.72rem; margin-left:2px;">2s</span>
            </div>
        </div>
    </div>

    <div class="container-main">

        {{-- Poli Switcher --}}
        <div class="poli-switcher">
            @foreach ($polis as $p)
                <a href="{{ route('queue-admin', $p->id) }}"
                   class="poli-btn {{ $p->id === $poli->id ? 'active' : '' }}">
                    {{ $p->name }}
                </a>
            @endforeach
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <span class="mdi mdi-check-circle"></span> {{ session('success') }}
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">
                <span class="mdi mdi-information"></span> {{ session('info') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple"><span class="mdi mdi-clock-outline"></span></div>
                <div class="stat-info">
                    <div class="num" id="stat-waiting">{{ $waitingQueues->count() }}</div>
                    <div class="lbl">Menunggu</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon violet"><span class="mdi mdi-account-voice"></span></div>
                <div class="stat-info">
                    <div class="num">{{ $current ? $current->queue_number : '—' }}</div>
                    <div class="lbl">Dipanggil</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon fuchsia"><span class="mdi mdi-account-off"></span></div>
                <div class="stat-info">
                    <div class="num" id="stat-late">{{ $lateQueues->count() }}</div>
                    <div class="lbl">Terlambat</div>
                </div>
            </div>
        </div>

        {{-- Current Queue --}}
        <div class="current-card">
            <div>
                <div class="label">Sedang Dipanggil</div>
                @if ($current)
                    <div class="number" id="current-number">{{ $current->queue_number }}</div>
                    <div class="name" id="current-name">{{ $current->customer_name }}</div>
                @else
                    <div class="no-current">
                        <span class="mdi mdi-timer-sand"></span>
                        Belum ada antrian dipanggil
                    </div>
                @endif
            </div>
            <div class="actions">
                <form id="form-call-next" action="{{ route('queue-call-next', $poli->id) }}" method="POST">
                    @csrf
                    <button id="btn-call-next" type="submit" class="btn-action btn-call-next">
                        @if ($current)
                            @if ($current->call_count > 3)
                                <span class="mdi mdi-account-off"></span> Tandai Late
                            @else
                                <span class="mdi mdi-bullhorn"></span>
                                Panggil Ulang
                                <span class="call-badge">{{ $current->call_count }}/3</span>
                            @endif
                        @else
                            <span class="mdi mdi-skip-next"></span> Panggil Pertama
                        @endif
                    </button>
                </form>
                @if ($current)
                    <form action="{{ route('queue-mark-done', $current->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-action btn-mark-done">
                            <span class="mdi mdi-check-circle"></span> Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Waiting & Late Lists --}}
        <div class="lists-row">
            {{-- Waiting --}}
            <div class="list-card">
                <div class="list-card-header waiting">
                    <span class="mdi mdi-clock-outline"></span>
                    <span id="waiting-header-count">Antrian Menunggu ({{ $waitingQueues->count() }})</span>
                </div>
                <div id="waiting-list">
                    @forelse ($waitingQueues as $q)
                        <div class="queue-item">
                            <span class="q-num">{{ $q->queue_number }}</span>
                            <span class="q-name">{{ $q->customer_name }}</span>
                            <div class="q-actions">
                                <form action="{{ route('queue-mark-late', $q->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-sm btn-late">
                                        <span class="mdi mdi-account-off"></span> Late
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="mdi mdi-check-all"></span>
                            Tidak ada antrian menunggu
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Late --}}
            <div class="list-card">
                <div class="list-card-header late">
                    <span class="mdi mdi-account-off"></span>
                    <span id="late-header-count">Antrian Terlambat ({{ $lateQueues->count() }})</span>
                </div>
                <div id="late-list">
                    @forelse ($lateQueues as $q)
                        <div class="queue-item">
                            <span class="q-num">{{ $q->queue_number }}</span>
                            <span class="q-name">{{ $q->customer_name }}</span>
                            <div class="q-actions">
                                <form action="{{ route('queue-call-late', $q->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-sm btn-recall">
                                        <span class="mdi mdi-phone"></span> Recall
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <span class="mdi mdi-check"></span>
                            Tidak ada antrian terlambat
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Done List --}}
        <div class="list-card" style="margin-top:20px;">
            <div class="list-card-header done">
                <span class="mdi mdi-check-circle-outline"></span>
                <span id="done-header-count">Antrian Selesai ({{ $doneQueues->count() }})</span>
            </div>
            <div id="done-list">
                @forelse ($doneQueues as $q)
                    <div class="queue-item">
                        <span class="q-num">{{ $q->queue_number }}</span>
                        <span class="q-name">{{ $q->customer_name }}</span>
                        <span class="q-time">{{ $q->finished_at ? \Carbon\Carbon::parse($q->finished_at)->format('H:i') : '' }}</span>
                    </div>
                @empty
                    <div class="empty-state">
                        <span class="mdi mdi-clipboard-check-outline"></span>
                        Belum ada antrian selesai hari ini
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        window.QueueSSEConfig = {
            streamUrl : '{{ route('queue-stream', $poli->id) }}',
            mode      : 'admin',
            csrfToken : '{{ csrf_token() }}',
        };
    </script>
    <script src="{{ asset('js/queue-sse.js') }}"></script>
    <script>
        (function () {
            const COUNTDOWN = 10;
            const STORAGE_KEY = 'queue_call_btn_until_{{ $poli->id }}';

            const btn      = document.getElementById('btn-call-next');
            const btnForm  = document.getElementById('form-call-next');

            if (!btn) return;

            function getRemainingSeconds() {
                const until = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
                return Math.max(0, Math.ceil((until - Date.now()) / 1000));
            }

            function startCountdown() {
                const until = Date.now() + COUNTDOWN * 1000;
                localStorage.setItem(STORAGE_KEY, until);
                tick();
            }

            function tick() {
                const remaining = getRemainingSeconds();
                if (remaining > 0) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="mdi mdi-timer-outline"></span> Tunggu ${remaining}s`;
                    setTimeout(tick, 1000);
                } else {
                    btn.disabled = false;
                    @if ($current)
                        @if ($current->call_count >= 3)
                            btn.innerHTML = '<span class="mdi mdi-account-off"></span> Tandai Late';
                        @else
                            btn.innerHTML = '<span class="mdi mdi-bullhorn"></span> Panggil Ulang <span class="call-badge">{{ $current->call_count }}/3</span>';
                        @endif
                    @else
                        btn.innerHTML = '<span class="mdi mdi-skip-next"></span> Panggil Pertama';
                    @endif
                }
            }

            // Cek sisa countdown dari localStorage saat load
            tick();

            // Mulai countdown saat form disubmit
            if (btnForm) {
                btnForm.addEventListener('submit', function () {
                    startCountdown();
                });
            }
        })();
    </script>
</body>
</html>
