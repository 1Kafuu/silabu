# Laporan Perubahan Sistem Kelola Buku

## 1. Dependensi

### 1.1 NPM Dependencies

| Package | Versi | Fungsi |
| --- | --- | --- |
| `html5-qrcode` | `^2.3.8` | Library client-side untuk scan barcode & QRCode via kamera |
| `bootstrap` | `^5.2.3` | UI framework untuk layout dan komponen |
| `@popperjs/core` | `^2.11.6` | Dependency Bootstrap untuk positioning |
| `axios` | `^1.11.0` | HTTP client untuk request AJAX |
| `tailwindcss` | `^4.0.0` | Utility-first CSS framework |
| `sass` | `^1.56.1` | CSS preprocessor |
| `vite` | `^7.0.7` | Build tool dan dev server |
| `laravel-vite-plugin` | `^2.0.0` | Plugin integrasi Vite dengan Laravel |
| `concurrently` | `^9.0.1` | Menjalankan banyak perintah secara paralel |

### 1.2 Composer Dependencies (PHP)

| Package | Versi | Fungsi |
| --- | --- | --- |
| `picqer/php-barcode-generator` | `^3.2` | Generate gambar barcode (CODE_128, EAN_13, dll) |
| `simplesoftwareio/simple-qrcode` | `^4.2` | Generate QRCode di sisi server |
| `barryvdh/laravel-dompdf` | `^3.1` | Generate PDF untuk laporan/cetak label |
| `midtrans/midtrans-php` | `^2.6` | Payment gateway |
| `laravel/socialite` | `^5.24` | OAuth authentication |
| `laravel/ui` | `^4.6` | Scaffold autentikasi |
| `laravel/framework` | `^12.0` | Framework utama |

---

## 2. Perubahan Kode

### 2.1 Fix Scanner Alur (`resources/views/admin/items/items.blade.php`)

**Masalah sebelumnya:**
- Variabel `isScanned` di-reset ke `false` segera setelah fetch selesai (baik sukses maupun error).
- Kamera tetap aktif, sehingga barcode yang masih di frame memicu callback scan berulang kali.
- Akibatnya: fetch dan alert/beep berulang untuk barcode yang sama.

**Perubahan:**

```diff
- let isScanned = false;
+ let lastScannedCode = null;

  document.getElementById('scanModal').addEventListener('show.bs.modal', function () {
-     isScanned = false;
+     lastScannedCode = null;
      // ...
  });

  function onScanSuccess(decodedText, decodedResult) {
-     if (isScanned) return;
-     isScanned = true;
+     if (decodedText === lastScannedCode) return;
+     lastScannedCode = decodedText;

      beepSound.play();

      fetch(`/items/barcode/${decodedText}`)
          .then(response => response.json())
          .then(data => {
              // ... tampilkan hasil
-             isScanned = false;
          })
          .catch(error => {
              // ... tampilkan error
-             isScanned = false;
          });
  }
```

**Dampak perubahan:**
- Barcode yang sama diabaikan selama masih di frame → tidak ada fetch berulang.
- Barcode lain tetap bisa langsung dipindai dan diproses tanpa menunggu modal ditutup.
- State `lastScannedCode` di-reset saat modal dibuka ulang.

---

## 3. Alur JavaScript Scan Barcode & QRCode

### 3.1 Inisialisasi Scanner

```
User klik tombol "Scan Label"
        │
        ▼
Modal `#scanModal` terbuka (event: show.bs.modal)
        │
        ▼
Reset state: lastScannedCode = null, hasil scan disembunyikan
        │
        ▼
Buat instance Html5QrcodeScanner
  └─ fps: 10
  └─ qrbox: 300x100 (format persegi panjang untuk barcode)
  └─ formatsToSupport: CODE_128, CODE_39, EAN_13, EAN_8,
                        UPC_A, UPC_E, CODABAR, ITF
        │
        ▼
html5QrcodeScanner.render(onScanSuccess, onScanFailure)
  └─ Kamera mulai aktif
```

### 3.2 Saat Barcode Terdeteksi (`onScanSuccess`)

```
Kamera mendeteksi kode → callback onScanSuccess(decodedText)
        │
        ▼
Guard: apakah decodedText === lastScannedCode?
  ├─ Ya → return (abaikan, kode sama masih di frame)
  └─ Tidak → lanjut
        │
        ▼
lastScannedCode = decodedText  ← catat kode ini
        │
        ▼
Mainkan beep sound
        │
        ▼
Fetch ke `/items/barcode/{decodedText}`
        │
        ├── Sukses (data.success === true)
        │       │
        │       ▼
        │   Tampilkan hasil di #scan-result:
        │     - result-id   → id_barang
        │     - result-nama → nama barang
        │     - result-harga → harga (format IDR)
        │
        └── Gagal / tidak ditemukan
                │
                ▼
            Alert: "Barang tidak ditemukan"
            atau
            Alert: "Terjadi kesalahan saat mencari barang"
```

### 3.3 Scanner Tetap Aktif Setelah Scan

```
Setelah fetch selesai (sukses/error):
        │
        ▼
lastScannedCode TIDAK di-reset → barcode yang sama diabaikan
        │
        ▼
Kamera tetap berjalan → bisa langsung scan barcode lain
        │
        ▼
Saat barcode baru terdeteksi (decodedText !== lastScannedCode):
        │
        ▼
Proses ulang dari awal (guard lolos → fetch → tampil hasil)
```

### 3.4 Saat Modal Ditutup

```
User tutup modal (event: hide.bs.modal)
        │
        ▼
html5QrcodeScanner.clear()
  └─ Hentikan kamera
  └─ Bersihkan resource
  └─ html5QrcodeScanner = null
```

### 3.5 Observer Styling (Bonus)

```
MutationObserver memantau elemen #reader
  └─ Saat tombol kamera (start/stop/select) muncul dari library
  └─ Override class Bootstrap:
       - btnStart → btn-gradient-primary
       - btnStop  → btn-danger
       - selectCamera → form-select
```

---

## 4. Ringkasan File yang Diubah

| File | Perubahan |
| --- | --- |
| `resources/views/admin/items/items.blade.php` | Ganti `isScanned` → `lastScannedCode`, hapus reset di `.then()/.catch()` |
