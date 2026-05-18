let html5QrcodeScanner = null;
let lastScannedCode = null;
let scannerObserver = null;

const barcodeConfig = window.barcodeScannerConfig || {};
const beepSound = barcodeConfig.beepUrl
    ? new Audio(barcodeConfig.beepUrl)
    : new Audio();

function getEl(id) {
    return document.getElementById(id);
}

function applyScannerUIFixes() {
    scannerObserver = new MutationObserver(() => {
        const btnStart = getEl("html5-qrcode-button-camera-start");
        const btnStop = getEl("html5-qrcode-button-camera-stop");
        const selectCamera = getEl("html5-qrcode-select-camera");

        if (btnStart && !btnStart.classList.contains("btn")) {
            btnStart.className = "btn btn-gradient-primary mt-2 mx-2";
            btnStart.style.cssText = "";
            btnStart.style.display = "inline-block";
        }
        if (btnStop && !btnStop.classList.contains("btn")) {
            btnStop.className = "btn btn-danger mt-2";
            btnStop.style.cssText = "";
            btnStop.style.display = "inline-block";
        }
        if (selectCamera && !selectCamera.classList.contains("form-select")) {
            selectCamera.className =
                "form-select form-select-sm mt-2 mb-2 d-inline-block";
            selectCamera.style.cssText = "width: 90%; color: #333;";
        }
    });

    const reader = getEl("reader");
    if (reader) {
        scannerObserver.observe(reader, { childList: true, subtree: true });
    }
}

function startScanner() {
    const reader = getEl("reader");
    if (!reader || html5QrcodeScanner) return;

    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
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
            ],
        },
        false,
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    applyScannerUIFixes();
}

function stopScanner() {
    if (scannerObserver) {
        scannerObserver.disconnect();
        scannerObserver = null;
    }
    if (!html5QrcodeScanner) return Promise.resolve();
    return html5QrcodeScanner
        .clear()
        .then(() => {
            html5QrcodeScanner = null;
        })
        .catch((error) => {
            console.error("Failed to clear html5QrcodeScanner", error);
        });
}

function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;

                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }

                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                }

                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error("Timeout, tidak dapat posisi"));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait },
        );
    });
}

function haversine(lat1, lng1, lat2, lng2) {
    const dLat = ((lat2 - lat1) * Math.PI) / 180;
    const dLng = ((lng2 - lng1) * Math.PI) / 180;
    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos((lat1 * Math.PI) / 180) *
            Math.cos((lat2 * Math.PI) / 180) *
            Math.sin(dLng / 2) ** 2;
    return 6371000 * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

let salesPosition = null;
let tokoData = null;
const THRESHOLD = 300;

function setLoading(show, text = "Memproses...") {
    const loading = getEl("scan-loading");
    const loadingText = getEl("scan-loading-text");
    const content = getEl("scan-result-content");
    if (loadingText) loadingText.textContent = text;
    if (loading) loading.classList.toggle("d-none", !show);
    if (content) content.classList.toggle("d-none", show);
}

function onScanSuccess(decodedText) {
    if (decodedText === lastScannedCode) return;
    lastScannedCode = decodedText;
    beepSound.play();

    setLoading(true, "🔍 Mencari data toko...");

    const lookupUrl = `${barcodeConfig.lookupUrl}/${encodeURIComponent(decodedText)}`;

    fetch(lookupUrl)
        .then((res) => res.json())
        .then(async (data) => {
            if (!data.success) {
                setLoading(false);
                alert(data.message || "Toko tidak ditemukan");
                return;
            }

            tokoData = data.data;
            const content = getEl("scan-result-content");
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-secondary text-start mb-2">
                            <strong>Nama Toko:</strong> ${tokoData.nama_toko}<br>
                            <strong>Latitude:</strong> ${tokoData.latitude}<br>
                            <strong>Longitude:</strong> ${tokoData.longtitude}<br>
                            <strong>Accuracy Toko:</strong> ${tokoData.accuracy} meter
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id="validasi-result" class="d-none">
                            <div id="validasi-alert" class="alert mb-0"></div>
                        </div>
                    </div>
                </div>
                <div id="btn-kirim-wrapper" class="mt-2 d-none">
                    <button type="button" id="btn-kirim-kunjungan" class="btn btn-primary">
                        <i class="mdi mdi-check-circle"></i> Kirim Kunjungan
                    </button>
                </div>
            `;

            getEl("btn-kirim-kunjungan").addEventListener(
                "click",
                kirimKunjungan,
            );

            if (!salesPosition) {
                setLoading(true, "📡 Mengambil lokasi GPS...");
                try {
                    salesPosition = await getAccuratePosition(50, 20000);
                } catch (err) {
                    setLoading(false);
                    alert("Gagal mengambil lokasi sales: " + err.message);
                    return;
                }
            }

            setLoading(true, "📐 Menghitung jarak...");
            await new Promise((r) => setTimeout(r, 500));

            const jarak = haversine(
                salesPosition.coords.latitude,
                salesPosition.coords.longitude,
                parseFloat(tokoData.latitude),
                parseFloat(tokoData.longtitude),
            );
            const thresholdEfektif =
                THRESHOLD +
                parseFloat(tokoData.accuracy) +
                salesPosition.coords.accuracy;
            const diterima = jarak <= thresholdEfektif;
            const status = diterima ? "diterima" : "ditolak";

            setLoading(false);

            const validasiAlert = getEl("validasi-alert");
            validasiAlert.className = `alert ${diterima ? "alert-success" : "alert-danger"} text-start mb-0`;
            validasiAlert.innerHTML = `
                <strong>${diterima ? "✅ DITERIMA" : "❌ DITOLAK"}</strong><br>
                Jarak: <strong>${Math.round(jarak)} meter</strong><br>
                Threshold efektif: ${Math.round(thresholdEfektif)} meter
                (${THRESHOLD} + ${Math.round(tokoData.accuracy)} + ${Math.round(salesPosition.coords.accuracy)})<br>
                Akurasi sales: ${salesPosition.coords.accuracy.toFixed(1)} meter
            `;
            getEl("validasi-result").classList.remove("d-none");
            getEl("btn-kirim-wrapper").classList.remove("d-none");

            // Reset lastScannedCode agar bisa scan ulang
            lastScannedCode = null;

            tokoData._kunjungan = {
                idtoko: tokoData.idtoko,
                latitude: salesPosition.coords.latitude,
                longitude: salesPosition.coords.longitude,
                accuracy: salesPosition.coords.accuracy,
                jarak: Math.round(jarak),
                status: status,
                waktu: new Date().toISOString(),
            };
        })
        .catch((err) => {
            setLoading(false);
            console.error(err);
            alert("Terjadi kesalahan saat mencari toko");
        });
}

function onScanFailure() {}


function kirimKunjungan() {
    if (!tokoData || !tokoData._kunjungan) return;

    const btn = getEl("btn-kirim-kunjungan");
    btn.disabled = true;
    btn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';

    fetch(window.salesConfig.storeSalesUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.salesConfig.csrfToken,
        },
        body: JSON.stringify(tokoData._kunjungan),
    })
        .then((res) => res.json())
        .then((data) => {
            btn.disabled = false;
            btn.innerHTML =
                '<i class="mdi mdi-check-circle"></i> Kirim Kunjungan';

            if (data.success) {
                getEl("scan-result-content").innerHTML =
                    '<p class="text-muted">Belum ada scan. Arahkan kamera ke barcode toko.</p>';
                lastScannedCode = null;

                const notif = getEl("notification-container");
                notif.innerHTML = `<div class="alert alert-success alert-dismissible fade show">
                Kunjungan berhasil dicatat!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
                setTimeout(() => (notif.innerHTML = ""), 5000);

                tambahRiwayat(
                    tokoData.nama_toko,
                    tokoData._kunjungan.jarak,
                    tokoData._kunjungan.accuracy,
                    tokoData._kunjungan.status,
                );
            } else {
                alert(data.message || "Gagal menyimpan kunjungan");
            }
        })
        .catch((err) => {
            btn.disabled = false;
            btn.innerHTML =
                '<i class="mdi mdi-check-circle"></i> Kirim Kunjungan';
            console.error(err);
            alert("Terjadi kesalahan saat mengirim kunjungan");
        });
}

function tambahRiwayat(namaToko, jarak, akurasiSales, status) {
    const tbody = getEl("riwayat-tbody");
    const empty = getEl("riwayat-empty");
    if (empty) empty.remove();

    const count = tbody.querySelectorAll("tr").length + 1;
    const badgeClass =
        status === "diterima" ? "badge bg-success" : "badge bg-danger";
    const statusLabel = status === "diterima" ? "✅ Diterima" : "❌ Ditolak";
    const waktu = new Date().toLocaleTimeString("id-ID");

    const tr = document.createElement("tr");
    tr.innerHTML = `
        <td>${String(count).padStart(2, "0")}</td>
        <td>${namaToko}</td>
        <td>${jarak} meter</td>
        <td>${akurasiSales.toFixed(1)} meter</td>
        <td><span class="${badgeClass}">${statusLabel}</span></td>
        <td>${waktu}</td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
}

window.addEventListener("load", function () {
    getAccuratePosition(50, 20000)
        .then((pos) => {
            salesPosition = pos;
        })
        .catch((err) => {
            console.warn("Gagal ambil lokasi awal:", err.message);
        });

    startScanner();
});
