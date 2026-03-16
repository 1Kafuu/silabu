@extends('layouts.app')

@section('title-page', 'POS - AJAX')
@section('page-title', 'Point of Sale (POS)')
@section('page-subtitle', 'AJAX')

@section('content')
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Keranjang Belanja (AJAX)</h4>
                    <div class="form-group">
                        <label>Pilih Barang</label>
                        <select class="form-select" id="pilih_barang" onchange="tambahKeKeranjang()">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->id_barang }}" data-nama="{{ $b->nama }}" data-harga="{{ $b->harga }}">
                                    [{{ $b->id_barang }}] {{ $b->nama }} - Rp {{ number_format($b->harga, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mt-3">
                            <thead class="bg-light">
                                <tr>
                                    <th>Barang</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keranjang-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center gap-3 d-flex flex-column">
                    <h4 class="card-title">Total Pembayaran</h4>
                    <div class="display-3 text-primary font-weight-bold mb-4" id="total-display">Rp 0</div>

                    <button type="button" class="btn btn-outline-danger btn-sm btn-block mt-3"
                        onclick="batalkanTransaksi()">
                        <i class="mdi mdi-cancel"></i> Batalkan Transaksi
                    </button>

                    <button type="button" class="btn btn-primary btn-lg btn-block" id="btn-bayar"
                        onclick="prosesTransaksi(this)" disabled>
                        <i class="mdi mdi-cash-multiple"></i> PROSES BAYAR
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-muted">Riwayat Transaksi (Terbaru di Atas)</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID Penjualan</th>
                                    <th>Waktu</th>
                                    <th>Kasir</th>
                                    <th>Total Transaksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayat as $r)
                                    <tr>
                                        <td>#{{ $r->id_penjualan }}</td>
                                        <td>{{ date('d M Y, H:i', strtotime($r->timestamp)) }}</td>
                                        <td>{{ $r->name }}</td>
                                        <td class="font-weight-bold text-info">Rp {{ number_format($r->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script>
        let keranjang = [];

        function tambahKeKeranjang() {
            const sel = $('#pilih_barang');
            const opt = sel.find(':selected');
            if (!opt.val()) return;

            const item = {
                id_barang: opt.val(),
                nama: opt.data('nama'),
                harga: parseInt(opt.data('harga')),
                qty: 1,
                subtotal: parseInt(opt.data('harga'))
            };

            const existing = keranjang.find(i => i.id_barang === item.id_barang);
            if (existing) {
                existing.qty++;
                existing.subtotal = existing.qty * existing.harga;
            } else {
                keranjang.push(item);
            }
            renderKeranjang();
            sel.val("");
        }

        function renderKeranjang() {
            let html = '';
            let total = 0;

            keranjang.forEach((item, index) => {
                total += item.subtotal;
                html += `<tr>
                                                    <td>${item.nama}</td>
                                                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                                                    <td>${item.qty}</td>
                                                    <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                                                    <td><button onclick="hapusItem(${index})" class="btn btn-danger btn-sm">X</button></td>
                                                </tr>`;
            });

            $('#keranjang-body').html(html || '<tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>');
            $('#total-display').text('Rp ' + total.toLocaleString('id-ID'));
            $('#btn-bayar').prop('disabled', keranjang.length === 0);
        }

        function hapusItem(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        function batalkanTransaksi() {
            if (keranjang.length === 0) return;
            Swal.fire({
                title: 'Batalkan Transaksi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batal!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    keranjang = [];
                    renderKeranjang();
                }
            });
        }

        function setButtonLoading(btn, text) {
            $(btn).prop('disabled', true); 
            $(btn).data('original-text', $(btn).html()); 
            $(btn).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + text);
        }

        function resetButtonLoading(btn) {
            $(btn).prop('disabled', false); 
            $(btn).html($(btn).data('original-text'));
        }

        function prosesTransaksi(btn) {
            const totalHarga = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
            setButtonLoading(btn, 'Menyimpan...');

            $.ajax({
                url: "{{ route('pos-store') }}",
                type: "POST",
                data: {
                    total_harga: totalHarga,
                    items: keranjang,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire('Berhasil!', res.msg, 'success').then(() => location.reload());
                    }
                },
                error: function () {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem', 'error');
                },
                complete: function () {
                    resetButtonLoading(btn);
                }
            });
        }
    </script>
@endpush