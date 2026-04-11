@extends('layouts.app')

@section('title-page', 'POS - Axios')
@section('page-title', 'Point of Sale (POS)')
@section('page-subtitle', 'Axios')

@section('content')
    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Keranjang Belanja (Axios)</h4>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Kode Barang</label>
                                <input type="text" class="form-control" id="pilih_barang" list="list_barang"
                                    placeholder="Ketik kode..." oninput="updateInfoBarang()"
                                    onkeypress="handleEnter(event)">
                                <datalist id="list_barang">
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id_barang }}" data-nama="{{ $b->nama }}"
                                            data-harga="{{ $b->harga }}">
                                            {{ $b->nama }}
                                        </option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Barang</label>
                                <input type="text" id="info_nama" class="form-control" readonly
                                    placeholder="Nama barang...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga</label>
                                <input type="text" id="info_harga" class="form-control" readonly placeholder="Rp 0">
                            </div>
                            <div class="col-md-2 text-center">
                                <label class="form-label">Qty</label>
                                <input type="number" id="jumlah_barang" class="form-control" value="0" min="1"
                                    oninput="cekInput()" onkeypress="handleEnter(event)">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-right d-flex justify-content-end">
                                <button type="button" id="btn-tambah" class="btn btn-success btn-lg px-5"
                                    style="background-color: #66e0c8; border-color: #66e0c8;" onclick="tambahKeKeranjang()"
                                    disabled>
                                    Tambah Ke Keranjang
                                </button>
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let keranjang = [];

        function updateInfoBarang() {
            const val = $('#pilih_barang').val();
            const opt = $('#list_barang option[value="' + val + '"]');

            if (opt.length > 0) {
                const nama = opt.data('nama');
                const harga = opt.data('harga');

                $('#info_nama').val(nama);
                $('#info_harga').val('Rp ' + harga.toLocaleString('id-ID'));

                // Fokuskan ke Qty jika kode valid
                if ($('#jumlah_barang').val() == 0) {
                    $('#jumlah_barang').val(1).focus();
                }
            } else {
                $('#info_nama').val('');
                $('#info_harga').val('');
            }
            cekInput();
        }

        function handleEnter(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const btn = $('#btn-tambah');
                if (!btn.prop('disabled')) {
                    tambahKeKeranjang();
                    $('#pilih_barang').focus();
                }
            }
        }

        function cekInput() {
            const kode = $('#pilih_barang').val();
            const qty = parseInt($('#jumlah_barang').val());
            const valid = $('#list_barang option[value="' + kode + '"]').length > 0;

            $('#btn-tambah').prop('disabled', !(valid && qty > 0));
        }

        function tambahKeKeranjang() {
            const inputKode = $('#pilih_barang');
            const inputJumlah = $('#jumlah_barang');
            const kode = inputKode.val();
            const qtyInput = parseInt(inputJumlah.val());

            // Cari data barang di datalist berdasarkan value input
            const opt = $('#list_barang option[value="' + kode + '"]');

            if (opt.length === 0 || qtyInput <= 0) return;

            const item = {
                id_barang: kode,
                nama: opt.data('nama'),
                harga: parseInt(opt.data('harga')),
                qty: qtyInput,
                subtotal: parseInt(opt.data('harga')) * qtyInput
            };

            const existing = keranjang.find(i => i.id_barang === item.id_barang);
            if (existing) {
                existing.qty += qtyInput;
                existing.subtotal = existing.qty * existing.harga;
            } else {
                keranjang.push(item);
            }

            renderKeranjang();

            // Reset Form Input
            inputKode.val("");
            $('#info_nama').val("");
            $('#info_harga').val("");
            inputJumlah.val(0);
            $('#btn-tambah').prop('disabled', true);

            inputKode.focus();
        }

        function renderKeranjang() {
            const body = document.getElementById('keranjang-body');
            const btnBayar = document.getElementById('btn-bayar');
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

            body.innerHTML = html || '<tr><td colspan="5" class="text-center text-muted">Belum ada barang di keranjang</td></tr>';
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
            btnBayar.disabled = keranjang.length === 0;
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

        async function prosesTransaksi(btn) {
            const totalHarga = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
            setButtonLoading(btn, 'Menyimpan...');

            try {
                const response = await axios.post("{{ route('pos-store') }}", {
                    total_harga: totalHarga,
                    items: keranjang,
                    _token: "{{ csrf_token() }}"
                });

                if (response.data.status === 'success') {
                    Swal.fire('Berhasil!', response.data.msg, 'success').then(() => location.reload());
                }
            } catch (error) {
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem', 'error');
            } finally {
                resetButtonLoading(btn);
            }
        }
    </script>
@endpush