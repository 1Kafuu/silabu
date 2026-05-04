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
        let html5QrcodeScanner = null;
        let isScanned = false;
        const beepSound = new Audio('{{ asset('music/scanner-beep.mp3') }}');

        function resetInvoiceView() {
            document.getElementById('invoice-order-id').textContent = '-';
            document.getElementById('invoice-customer').textContent = '-';
            document.getElementById('invoice-payment-method').textContent = '-';
            document.getElementById('invoice-scanned-at').textContent = '-';
            document.getElementById('invoice-total').textContent = 'Rp 0';
            document.getElementById('invoice-status-badge').className = 'badge rounded-pill bg-secondary';
            document.getElementById('invoice-status-badge').textContent = 'Menunggu';
            document.getElementById('invoice-items').innerHTML = '<div class="text-center text-muted py-4">Belum ada data pesanan</div>';
            document.getElementById('btn-scan-ulang').classList.add('d-none');
        }

        function renderInvoice(data) {
            document.getElementById('btn-scan-ulang').classList.remove('d-none');
            document.getElementById('invoice-order-id').textContent = '#' + data.order_id;
            document.getElementById('invoice-customer').textContent = data.customer_name || '-';
            document.getElementById('invoice-payment-method').textContent = data.metode_bayar || '-';
            document.getElementById('invoice-scanned-at').textContent = data.scanned_at || '-';
            document.getElementById('invoice-total').textContent = data.total_formatted || 'Rp 0';
            document.getElementById('invoice-status-badge').className = 'badge rounded-pill ' + (data.status_badge || 'bg-secondary');
            document.getElementById('invoice-status-badge').textContent = data.status_label || 'Unknown';

            const items = Array.isArray(data.items) ? data.items : [];
            document.getElementById('invoice-items').innerHTML = items.length
                ? items.map(item => `
                    <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
                        <div class="pe-2 w-100">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <div class="fw-semibold">${item.nama_menu}</div>
                                    <div class="text-muted small">${item.harga_formatted} × ${item.qty}</div>
                                </div>
                                <div class="fw-semibold text-end text-nowrap">${item.subtotal_formatted}</div>
                            </div>
                        </div>
                    </div>
                `).join('')
                : '<div class="text-center text-muted py-4">Belum ada data pesanan</div>';
        }

        function applyScannerUIFixes() {
            const observer = new MutationObserver(() => {
                const btnStart = document.getElementById('html5-qrcode-button-camera-start');
                const btnStop = document.getElementById('html5-qrcode-button-camera-stop');
                const selectCamera = document.getElementById('html5-qrcode-select-camera');

                if (btnStart && !btnStart.classList.contains('btn')) {
                    btnStart.className = 'btn btn-gradient-primary mt-2 mx-2';
                    btnStart.style.cssText = '';
                    btnStart.style.display = 'inline-block';
                }

                if (btnStop && !btnStop.classList.contains('btn')) {
                    btnStop.className = 'btn btn-danger mt-2';
                    btnStop.style.cssText = '';
                    btnStop.style.display = 'inline-block';
                }

                if (selectCamera && !selectCamera.classList.contains('form-select')) {
                    selectCamera.className = 'form-select form-select-sm mt-2 mb-2 d-inline-block';
                    selectCamera.style.cssText = 'width: 90%; color: #333;';
                }
            });

            observer.observe(document.getElementById('reader'), { childList: true, subtree: true });
        }

        function startScanner() {
            if (html5QrcodeScanner) return;

            html5QrcodeScanner = new Html5QrcodeScanner('reader', {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
            }, false);

            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            applyScannerUIFixes();
        }

        function closeScanner() {
            if (!html5QrcodeScanner) return Promise.resolve();
            return html5QrcodeScanner.clear().then(() => {
                html5QrcodeScanner = null;
            }).catch(error => {
                console.error('Failed to clear html5QrcodeScanner', error);
            });
        }

        function restartScanner() {
            isScanned = false;
            resetInvoiceView();
            closeScanner().finally(() => {
                startScanner();
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isScanned) return;
            isScanned = true;

            beepSound.play();

            const url = `{{ route('find-by-qrcode', ['id' => '__ID__']) }}`.replace('__ID__', decodedText);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderInvoice(data.data);
                        closeScanner();
                    } else {
                        alert(data.message || 'Pesanan tidak ditemukan');
                        isScanned = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mencari pesanan');
                    isScanned = false;
                });
        }

        function onScanFailure(error) {}

        document.addEventListener('DOMContentLoaded', function () {
            resetInvoiceView();
            startScanner();
        });
    </script>
@endpush