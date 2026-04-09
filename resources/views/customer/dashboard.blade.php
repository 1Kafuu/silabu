@extends('layouts.customer')

@section('title', 'Workshop Framework - Customer Dashboard')
@section('page-title', 'Canteen Dashboard')
@section('page-subtitle', 'Pick your meal and enjoy!')

@section('content')
    <div class="row col-lg-12">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">Menu</h4>
                    <div class="form-group">
                        <!-- Card-Menu dengan dropdown memilih vendor -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card shadow-sm border-info">
                <div class="card-body d-flex flex-column" style="height: 100%;">
                    <h4 class="card-title text-center">Total Pembayaran</h4>
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary font-weight-bold" id="total-display" style="font-size: 35px;">Rp 0</div>
                    </div>
                    <!-- List Items -->
                    <div class="flex-grow-1 mb-3" style="min-height: 200px; max-height: 300px; overflow-y: auto; border-radius: 4px; padding: 8px;">
                        <div id="keranjang-list" class="text-muted text-center" style="padding: 20px 0;">
                            Belum ada barang
                        </div>
                    </div>

                    <!-- Buttons -->
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2"
                        onclick="batalkanTransaksi()">
                        <i class="mdi mdi-cancel"></i> Batalkan
                    </button>

                    <button type="button" class="btn btn-primary btn-lg w-100" id="btn-bayar"
                        onclick="prosesTransaksi(this)" disabled>
                        <i class="mdi mdi-cash-multiple"></i> BAYAR
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
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="font-weight-bold text-info"></td>
                                    </tr>
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
            const listDiv = document.getElementById('keranjang-list');
            const btnBayar = document.getElementById('btn-bayar');
            let html = '';
            let total = 0;

            keranjang.forEach((item, index) => {
                total += item.subtotal;
                html += `<div style="padding: 8px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">${item.nama}</div>
                            <div style="color: #666; font-size: 0.85rem;">Rp ${item.harga.toLocaleString('id-ID')}</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 4px; margin: 0 8px;">
                            <button type="button" class="btn btn-sm p-0" style="width: 22px; height: 22px; padding: 0 !important; font-size: 0.8rem;" onclick="updateQty(${index}, -1)" ${item.qty <= 1 ? 'disabled' : ''}>−</button>
                            <span style="min-width: 20px; text-align: center; font-weight: 600;">${item.qty}</span>
                            <button type="button" class="btn btn-sm p-0" style="width: 22px; height: 22px; padding: 0 !important; font-size: 0.8rem;" onclick="updateQty(${index}, 1)">+</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-link p-0" style="color: #dc3545; text-decoration: none;" onclick="hapusItem(${index})">Hapus</button>
                    </div>`;
            });

            listDiv.innerHTML = html || '<div style="padding: 20px 0; text-align: center; color: #999;">Belum ada barang</div>';
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
            btnBayar.disabled = keranjang.length === 0;
        }

        function updateQty(index, delta) {
            const item = keranjang[index];
            if (!item) return;
            const nextQty = item.qty + delta;
            if (nextQty < 1) return;
            item.qty = nextQty;
            item.subtotal = item.qty * item.harga;
            renderKeranjang();
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