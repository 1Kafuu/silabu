let html5QrcodeScanner = null;
let isScanned = false;
let scannerObserver = null;

const qrcodeConfig = window.qrcodeScannerConfig || {};
const beepSound = qrcodeConfig.beepUrl ? new Audio(qrcodeConfig.beepUrl) : new Audio();

function getEl(id) {
    return document.getElementById(id);
}

function resetInvoiceView() {
    const invoiceOrderId = getEl('invoice-order-id');
    const invoiceCustomer = getEl('invoice-customer');
    const invoicePaymentMethod = getEl('invoice-payment-method');
    const invoiceScannedAt = getEl('invoice-scanned-at');
    const invoiceTotal = getEl('invoice-total');
    const invoiceStatusBadge = getEl('invoice-status-badge');
    const invoiceItems = getEl('invoice-items');
    const btnScanUlang = getEl('btn-scan-ulang');

    if (invoiceOrderId) invoiceOrderId.textContent = '-';
    if (invoiceCustomer) invoiceCustomer.textContent = '-';
    if (invoicePaymentMethod) invoicePaymentMethod.textContent = '-';
    if (invoiceScannedAt) invoiceScannedAt.textContent = '-';
    if (invoiceTotal) invoiceTotal.textContent = 'Rp 0';
    if (invoiceStatusBadge) {
        invoiceStatusBadge.className = 'badge rounded-pill bg-secondary';
        invoiceStatusBadge.textContent = 'Menunggu';
    }
    if (invoiceItems) {
        invoiceItems.innerHTML = '<div class="text-center text-muted py-4">Belum ada data pesanan</div>';
    }
    if (btnScanUlang) btnScanUlang.classList.add('d-none');
}

function renderInvoice(data) {
    const btnScanUlang = getEl('btn-scan-ulang');
    const invoiceOrderId = getEl('invoice-order-id');
    const invoiceCustomer = getEl('invoice-customer');
    const invoicePaymentMethod = getEl('invoice-payment-method');
    const invoiceScannedAt = getEl('invoice-scanned-at');
    const invoiceTotal = getEl('invoice-total');
    const invoiceStatusBadge = getEl('invoice-status-badge');
    const invoiceItems = getEl('invoice-items');

    if (btnScanUlang) btnScanUlang.classList.remove('d-none');
    if (invoiceOrderId) invoiceOrderId.textContent = '#' + data.order_id;
    if (invoiceCustomer) invoiceCustomer.textContent = data.customer_name || '-';
    if (invoicePaymentMethod) invoicePaymentMethod.textContent = data.metode_bayar || '-';
    if (invoiceScannedAt) invoiceScannedAt.textContent = data.scanned_at || '-';
    if (invoiceTotal) invoiceTotal.textContent = data.total_formatted || 'Rp 0';
    if (invoiceStatusBadge) {
        invoiceStatusBadge.className = 'badge rounded-pill ' + (data.status_badge || 'bg-secondary');
        invoiceStatusBadge.textContent = data.status_label || 'Unknown';
    }

    const items = Array.isArray(data.items) ? data.items : [];
    if (invoiceItems) {
        invoiceItems.innerHTML = items.length
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
}

function applyScannerUIFixes() {
    scannerObserver = new MutationObserver(() => {
        const btnStart = getEl('html5-qrcode-button-camera-start');
        const btnStop = getEl('html5-qrcode-button-camera-stop');
        const selectCamera = getEl('html5-qrcode-select-camera');

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

    const reader = getEl('reader');
    if (reader) {
        scannerObserver.observe(reader, { childList: true, subtree: true });
    }
}

function startScanner() {
    const reader = getEl('reader');
    if (!reader || html5QrcodeScanner) return;

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
    if (scannerObserver) {
        scannerObserver.disconnect();
        scannerObserver = null;
    }

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

function onScanSuccess(decodedText) {
    if (isScanned) return;
    isScanned = true;

    beepSound.play();

    const urlTemplate = qrcodeConfig.findUrl || '';
    const url = urlTemplate.replace('__ID__', encodeURIComponent(decodedText));

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

function onScanFailure() {
}

function initQrScanner() {
    resetInvoiceView();
    startScanner();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQrScanner);
} else {
    initQrScanner();
}
