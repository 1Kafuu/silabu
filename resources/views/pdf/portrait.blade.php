<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daftar Buku</title>
    <style>
        /* Setting ukuran A4 */
        @page {
            size: A4;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            /* Ukuran font lebih kecil untuk A4 */
            line-height: 1.3;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }

        .header h2 {
            margin: 0;
            color: #333;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0 0;
            color: #666;
            font-size: 9pt;
        }

        /* Tabel dengan layout fixed untuk konsistensi */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
            font-size: 9pt;
        }

        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 6px 4px;
            text-align: left;
            border: 1px solid #1a2632;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 5px 4px;
            border: 1px solid #bdc3c7;
            vertical-align: middle;
            word-wrap: break-word;
            /* Memastikan teks panjang terpotong */
        }

        /* Warna selang-seling untuk baris */
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 8pt;
            color: #7f8c8d;
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #bdc3c7;
        }

        /* Informasi tambahan */
        .info-section {
            margin-bottom: 10px;
            font-size: 9pt;
        }

        .info-item {
            display: inline-block;
            margin-right: 20px;
        }

        /* Page break jika diperlukan */
        .page-break {
            page-break-after: always;
        }

        /* Untuk teks yang terlalu panjang */
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Nomor urut */
        .no-column {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN DAFTAR BUKU</h2>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        <span class="info-item"><strong>Total Buku:</strong> {{ $data->count() }}</span>
        <span class="info-item"><strong>Dicetak oleh:</strong> {{ auth()->user()?->name ?? 'System' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" style="text-align: center">No</th>
                <th width="12%">UID</th>
                <th width="30%">Title</th>
                <th width="20%">Author</th>
                <th width="18%">Category</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
                <tr>
                    <td class="no-column">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        {{ $row->kode }}
                    </td>
                    <td>
                        {{ $row->judul }}
                    </td>
                    <td>
                        {{ $row->pengarang }}
                    </td>
                    <td>
                        {{ $row->category->nama_kategori ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        <em>Tidak ada data buku tersedia</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Halaman ini dicetak pada {{ date('d/m/Y H:i:s') }} | Dokumen ini sah dicetak dari sistem</p>
    </div>
</body>

</html>