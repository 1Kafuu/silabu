let html5QrcodeScanner = null;
let lastScannedCode = null;
let scannerObserver = null;

const barcodeConfig = window.barcodeScannerConfig || {};
const beepSound = barcodeConfig.beepUrl ? new Audio(barcodeConfig.beepUrl) : new Audio();

function getEl(id) {
    return document.getElementById(id);
}

function resetScanResult() {
    const scanResult = getEl('scan-result');
    if (scanResult) scanResult.classList.add('d-none');

    const resultId = getEl('result-id');
    const resultNama = getEl('result-nama');
    const resultHarga = getEl('result-harga');

    if (resultId) resultId.textContent = '';
    if (resultNama) resultNama.textContent = '';
    if (resultHarga) resultHarga.textContent = '';
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
        qrbox: { width: 300, height: 100 },
        rememberLastUsedCamera: true,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        formatsToSupport: [
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.CODABAR,
            Html5QrcodeSupportedFormats.ITF,
        ]
    }, false);

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    applyScannerUIFixes();
}

function stopScanner() {
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

function onScanSuccess(decodedText) {
    if (decodedText === lastScannedCode) return;
    lastScannedCode = decodedText;

    beepSound.play();

    const baseUrl = barcodeConfig.lookupUrl || '';
    const lookupUrl = `${baseUrl}/${encodeURIComponent(decodedText)}`;

    fetch(lookupUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const resultId = getEl('result-id');
                const resultNama = getEl('result-nama');
                const resultHarga = getEl('result-harga');
                const scanResult = getEl('scan-result');

                if (resultId) resultId.textContent = data.data.id_barang;
                if (resultNama) resultNama.textContent = data.data.nama;
                if (resultHarga) {
                    resultHarga.textContent = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR'
                    }).format(data.data.harga);
                }
                if (scanResult) scanResult.classList.remove('d-none');
            } else {
                alert(data.message || 'Barang tidak ditemukan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mencari barang');
        });
}

function onScanFailure() {
}

function initBarcodeScanner() {
    const scanModal = getEl('scanModal');
    if (!scanModal) return;

    scanModal.addEventListener('show.bs.modal', function () {
        lastScannedCode = null;
        resetScanResult();
        startScanner();
    });

    scanModal.addEventListener('hide.bs.modal', function () {
        stopScanner();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBarcodeScanner);
} else {
    initBarcodeScanner();
}
