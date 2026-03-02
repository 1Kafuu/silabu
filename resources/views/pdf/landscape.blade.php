<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Kategori</title>
    <style>
        /* Setting ukuran A4 Landscape */
        @page {
            size: A4 landscape;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
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
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 3px 0 0;
            color: #666;
            font-size: 10pt;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1a2632;
            font-size: 10pt;
        }
        
        td {
            padding: 6px;
            border: 1px solid #bdc3c7;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
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
        
        .info-section {
            margin-bottom: 10px;
            font-size: 10pt;
            display: flex;
            justify-content: space-between;
        }
        
        /* Lebar kolom untuk landscape */
        .no-column {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DAFTAR KATEGORI</h2>
        <p>Tanggal Cetak: {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="info-section">
        <span><strong>Total Kategori:</strong> {{ $data->count() }}</span>
        <span><strong>Dicetak oleh:</strong> {{auth()->user()->name ?? 'System' }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%" style="text-align: center">No</th>
                <th width="10%">Category</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $row)
            <tr>
                <td style="text-align: center">
                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </td>
                <td>
                    <strong>{{ $row->nama_kategori }}</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    <em>Tidak ada data kategori tersedia</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Halaman {{ $loop->iteration ?? 1 }} dari 1 | Dicetak: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>