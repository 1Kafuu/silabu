@extends('layouts.sales')
@section('title', 'Dashboard Sales')
@section('page-title', 'Sales')
@section('page-subtitle', 'Kunjungan Toko')

@section('content')
    <div id="notification-container"></div>

    <div class="row align-items-stretch">
        {{-- Kolom Scanner --}}
        <div class="col-md-5 grid-margin d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <h4 class="card-title">Scan Barcode Toko</h4>
                    <div id="reader" style="width: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Hasil Scan + Riwayat --}}
        <div class="col-md-7 grid-margin d-flex flex-column">
            {{-- Hasil Scan --}}
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">Hasil Scan</h4>

                    {{-- Loading --}}
                    <div id="scan-loading" class="text-center d-none py-3">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted mb-0" id="scan-loading-text">Memproses...</p>
                    </div>

                    {{-- Konten hasil --}}
                    <div id="scan-result-content">
                        <p class="text-muted">Belum ada scan. Arahkan kamera ke barcode toko.</p>
                    </div>
                </div>
            </div>

            {{-- Riwayat Kunjungan --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Riwayat Kunjungan Hari Ini</h4>
                    <div class="table-responsive">
                        <table class="table" id="tabel-riwayat">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Toko</th>
                                    <th>Jarak</th>
                                    <th>Akurasi</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="riwayat-tbody">
                                @forelse ($riwayat as $index => $row)
                                    <tr>
                                        <td>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $row->toko->nama_toko ?? '-' }}</td>
                                        <td>{{ $row->jarak }} meter</td>
                                        <td>{{ number_format($row->accuracy, 1) }} meter</td>
                                        <td>
                                            @if (trim($row->status) === 'diterima')
                                                <span class="badge bg-success">✅ Diterima</span>
                                            @else
                                                <span class="badge bg-danger">❌ Ditolak</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($row->waktu)->format('H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr id="riwayat-empty">
                                        <td colspan="6" class="text-center text-muted">Belum ada kunjungan hari ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        window.barcodeScannerConfig = {
            beepUrl: '{{ asset('music/scanner-beep.mp3') }}',
            lookupUrl: '{{ url('sales/barcode') }}'
        };
        window.salesConfig = {
            storeSalesUrl: '{{ route('store-sales') }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    <script src="{{ asset('js/sales.js') }}"></script>
@endpush

@push('style-page')
    <style>
        #reader { width: 100%; }
        #reader video { width: 100% !important; }
    </style>
@endpush
