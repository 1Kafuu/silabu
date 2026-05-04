@extends('layouts.vendor')

@section('title', 'Scan Pesanan')
@section('page-title', 'Scan QR Code')
@section('page-subtitle', 'QR Scanner')

@section('content')
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="card-title mb-2">Scanner QR</h4>
                    <p class="text-muted mb-4">Arahkan kamera ke QR pesanan untuk menampilkan invoice.</p>

                    <div id="reader" style="width: 100%; text-align: center;"></div>

                    <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                        <button id="btn-scan-ulang" type="button" class="btn btn-outline-primary d-none" onclick="restartScanner()">
                            <i class="mdi mdi-refresh me-1"></i> Scan Ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="card-title mb-1">Invoice</h4>
                            <p class="text-muted mb-0">Detail pesanan hasil scan QR</p>
                        </div>
                        <span id="invoice-status-badge" class="badge rounded-pill bg-secondary">Menunggu</span>
                    </div>

                    <div id="scan-result">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="small text-muted">ID Pesanan</div>
                                    <div class="fw-bold" id="invoice-order-id">-</div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="small text-muted">Pelanggan</div>
                                    <div class="fw-bold" id="invoice-customer">-</div>
                                </div>
                            </div>
                        </div>

                        <div id="invoice-items" class="border rounded p-2 bg-light mb-3">
                            <div class="text-center text-muted py-4">Belum ada data pesanan</div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Metode Bayar</span>
                            <span id="invoice-payment-method" class="fw-semibold">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Waktu Scan</span>
                            <span id="invoice-scanned-at" class="fw-semibold">-</span>
                        </div>
                        <div class="d-flex justify-content-between fs-5 fw-bold">
                            <span>Total</span>
                            <span id="invoice-total">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        window.qrcodeScannerConfig = {
            beepUrl: '{{ asset('music/scanner-beep.mp3') }}',
            findUrl: '{{ route('find-by-qrcode', ['id' => '__ID__']) }}'
        };
    </script>
    <script src="{{ asset('js/qrcode.js') }}"></script>
@endpush
