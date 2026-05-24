/**
 * nfc-scanner.js
 * Reusable Web NFC API scanner module.
 *
 * Cara pakai — taruh config sebelum include script ini di blade:
 *
 *   <script>
 *     window.NFCScannerConfig = {
 *       targetInput  : '#serial_number',   // selector input yang akan diisi
 *       scanButton   : '#btn-scan-nfc',    // selector tombol scan
 *       statusEl     : '#nfc-status',      // selector elemen status (opsional)
 *       onScan       : function(serialNumber) {},  // callback saat berhasil scan (opsional)
 *       onError      : function(message) {},       // callback saat error (opsional)
 *       autoStop     : true,               // stop scan setelah 1 kali berhasil (default: true)
 *     };
 *   </script>
 *   <script src="{{ asset('js/nfc-scanner.js') }}"></script>
 */

(function () {
    const cfg = Object.assign({
        targetInput : null,
        scanButton  : null,
        statusEl    : null,
        onScan      : null,
        onError     : null,
        autoStop    : true,
    }, window.NFCScannerConfig || {});

    // ── Guards ────────────────────────────────────────────────
    if (!cfg.targetInput || !cfg.scanButton) {
        console.error('[nfc-scanner] targetInput and scanButton are required in NFCScannerConfig.');
        return;
    }

    if (!('NDEFReader' in window)) {
        console.warn('[nfc-scanner] Web NFC API is not supported in this browser. Use Chrome on Android.');
        setStatus('Web NFC tidak didukung di browser ini. Gunakan Chrome di Android.', 'danger');
        disableButton();
        return;
    }

    // ── State ─────────────────────────────────────────────────
    let reader      = null;
    let isScanning  = false;
    let abortCtrl   = null;

    // ── DOM helpers ───────────────────────────────────────────
    function getInput() {
        return document.querySelector(cfg.targetInput);
    }

    function getButton() {
        return document.querySelector(cfg.scanButton);
    }

    function setStatus(message, type) {
        if (!cfg.statusEl) return;
        const el = document.querySelector(cfg.statusEl);
        if (!el) return;

        const typeMap = {
            info    : 'text-info',
            success : 'text-success',
            danger  : 'text-danger',
            warning : 'text-warning',
            muted   : 'text-muted',
        };

        el.className = typeMap[type] || 'text-muted';
        el.textContent = message;
    }

    function setButtonState(scanning) {
        const btn = getButton();
        if (!btn) return;

        if (scanning) {
            btn.innerHTML = '<i class="mdi mdi-nfc-search-variant"></i> Scanning...';
            btn.disabled  = false;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-warning');
        } else {
            btn.innerHTML = '<i class="mdi mdi-nfc"></i> Scan NFC';
            btn.disabled  = false;
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-primary');
        }
    }

    function disableButton() {
        const btn = getButton();
        if (!btn) return;
        btn.disabled = true;
        btn.classList.add('btn-secondary');
        btn.classList.remove('btn-primary');
    }

    // ── Core scan logic ───────────────────────────────────────
    async function startScan() {
        if (isScanning) {
            stopScan();
            return;
        }

        try {
            isScanning = true;
            abortCtrl  = new AbortController();
            reader     = new NDEFReader();

            setButtonState(true);
            setStatus('Mendekatkan kartu NFC...', 'info');

            await reader.scan({ signal: abortCtrl.signal });

            reader.addEventListener('reading', ({ serialNumber }) => {
                const serial = serialNumber.toUpperCase().replace(/:/g, '');

                const input = getInput();
                if (input) {
                    input.value = serial;
                    // Trigger change event so frameworks (jQuery Validate, etc.) pick it up
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                setStatus('Kartu terdeteksi: ' + serial, 'success');

                if (typeof cfg.onScan === 'function') {
                    cfg.onScan(serial);
                }

                if (cfg.autoStop) {
                    stopScan();
                }
            });

            reader.addEventListener('readingerror', () => {
                const msg = 'Gagal membaca kartu NFC. Coba lagi.';
                setStatus(msg, 'danger');
                if (typeof cfg.onError === 'function') cfg.onError(msg);
                stopScan();
            });

        } catch (err) {
            isScanning = false;
            setButtonState(false);

            let msg = 'Gagal memulai scan NFC.';

            if (err.name === 'NotAllowedError') {
                msg = 'Izin NFC ditolak. Berikan izin NFC di browser.';
            } else if (err.name === 'NotSupportedError') {
                msg = 'NFC tidak tersedia di perangkat ini.';
            } else {
                msg = 'Error: ' + err.message;
            }

            setStatus(msg, 'danger');
            if (typeof cfg.onError === 'function') cfg.onError(msg);
            console.error('[nfc-scanner]', err);
        }
    }

    function stopScan() {
        if (abortCtrl) {
            abortCtrl.abort();
            abortCtrl = null;
        }
        isScanning = false;
        reader     = null;
        setButtonState(false);

        const input = getInput();
        if (input && !input.value) {
            setStatus('Scan dibatalkan.', 'muted');
        }
    }

    // ── Init ──────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        const btn = getButton();
        if (!btn) {
            console.error('[nfc-scanner] Scan button not found:', cfg.scanButton);
            return;
        }

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            startScan();
        });

        setStatus('Tekan tombol Scan NFC untuk memulai.', 'muted');
    });

})();
