<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Antrian - {{ $queue->queue_number }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/queue/ticket.css') }}">
</head>

</head>
<body>
    <div class="ticket-wrapper">
        <div class="ticket-card">

            {{-- Header --}}
            <div class="ticket-header">
                <div class="icon"><span class="mdi mdi-hospital-building"></span></div>
                <h2>Tiket Antrian</h2>
                <div class="poli-name">{{ $queue->poli->name }}</div>
            </div>

            {{-- Number --}}
            <div class="ticket-number-section">
                <div class="ticket-number-label">Nomor Antrian Anda</div>
                <div class="ticket-number">{{ $queue->queue_number }}</div>
            </div>

            {{-- Details --}}
            <div class="ticket-body">
                <div class="ticket-row">
                    <span class="label">
                        <span class="mdi mdi-account"></span> Nama
                    </span>
                    <span class="value">{{ $queue->customer_name }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">
                        <span class="mdi mdi-medical-bag"></span> Poli
                    </span>
                    <span class="value">{{ $queue->poli->name }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">
                        <span class="mdi mdi-clock-outline"></span> Waktu Daftar
                    </span>
                    <span class="value">{{ $queue->created_at->format('H:i, d M Y') }}</span>
                </div>
                <div class="ticket-row">
                    <span class="label">
                        <span class="mdi mdi-information-outline"></span> Status
                    </span>
                    <span class="value">
                        <span class="status-badge">
                            <span class="status-dot"></span>
                            Menunggu
                        </span>
                    </span>
                </div>
            </div>

            {{-- Footer --}}
            <div class="ticket-footer">
                <p>Harap menunggu hingga nomor Anda dipanggil di papan antrian.</p>
                <a href="{{ route('queue-guest') }}" class="btn-back">
                    <span class="mdi mdi-plus"></span> Ambil Antrian Lain
                </a>
            </div>

        </div>
    </div>
</body>
</html>
