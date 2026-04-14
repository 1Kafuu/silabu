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

                    <!-- Vendor Filter Dropdown -->
                    <div class="form-group mb-3">
                        <label for="vendor-filter" class="form-label">Pilih Vendor:</label>
                        <select id="vendor-filter" class="form-select">
                            <option value="all">Semua Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Menu Cards Container -->
                    <div id="menu-cards-container" class="row">
                        @if ($menus->count() > 0)
                            @foreach ($menus as $menu)
                                <div class="col-md-4 mb-3 menu-card" data-vendor-id="{{ $menu->idvendor }}">
                                    <div class="card shadow-sm menu-card-inner" style="height: 100%;">
                                        <img src="{{ asset('storage/' . $menu->path_gambar) }}" class="card-img-top"
                                            alt="{{ $menu->nama_menu }}" style="height: 150px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $menu->nama_menu }}</h5>
                                            <p class="card-text text-muted small">
                                                <i class="mdi mdi-store"></i> {{ $menu->vendor->nama_vendor }}
                                            </p>
                                            <h4 class="text-primary fw-bold">
                                                {{ Illuminate\Support\Number::currency($menu->harga, 'IDR', 'id') }}
                                            </h4>
                                            <button type="button" class="btn btn-primary w-100 btn-tambah-keranjang"
                                                data-idmenu="{{ $menu->idmenu }}" data-nama="{{ $menu->nama_menu }}"
                                                data-harga="{{ $menu->harga }}" data-vendor="{{ $menu->vendor->nama_vendor }}">
                                                <i class="mdi mdi-plus"></i> Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    Tidak ada menu tersedia dari vendor aktif.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card shadow-sm border-info">
                <div class="card-body d-flex flex-column" style="height: 100%;">
                    <h4 class="card-title text-center">Total Pembayaran</h4>
                    <div class="text-center mb-3">
                        <div class="display-4 text-primary font-weight-bold" id="total-display" style="font-size: 35px;">Rp
                            0</div>
                    </div>
                    <!-- List Items -->
                    <div class="flex-grow-1 mb-3"
                        style="min-height: 200px; max-height: 300px; overflow-y: auto; border-radius: 4px; padding: 8px;">
                        <div id="keranjang-list" class="text-muted text-center" style="padding: 20px 0;">
                            Belum ada barang
                        </div>
                    </div>

                    <!-- Buttons -->
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="batalkanTransaksi()">
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
                                    <th>Metode Pembayaran</th>
                                    <th>Status Bayar</th>
                                    <th>Total Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="transaksi-table-body">
                                <x-customer-transactions-table :pesanan="$pesanan" />
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrcode-container" class="mb-3">
                    </div>
                    <h4 id="qr-order-id" class="fw-bold text-primary"></h4>
                    <p class="text-muted small">Tunjukkan QR ini ke kasir</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    <script>
        // Keranjang belanja
        let keranjang = [];

        // Render tampilan keranjang
        function renderKeranjang() {
            const listDiv = document.getElementById('keranjang-list');
            const btnBayar = document.getElementById('btn-bayar');
            let html = '';
            let total = 0;

            keranjang.forEach((item, index) => {
                total += item.subtotal;
                html += `<div style="padding: 8px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                                                                            <div style="flex: 1;">
                                                                                <div style="font-weight: 600;">${item.nama_menu}</div>
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

        // Update quantity item di keranjang
        function updateQty(index, delta) {
            const item = keranjang[index];
            if (!item) return;
            const nextQty = item.qty + delta;
            if (nextQty < 1) return;
            item.qty = nextQty;
            item.subtotal = item.qty * item.harga;
            renderKeranjang();
        }

        // Hapus item dari keranjang
        function hapusItem(index) {
            keranjang.splice(index, 1);
            renderKeranjang();
        }

        // Batalkan transaksi
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

        // Set tombol loading
        function setButtonLoading(btn, text) {
            $(btn).prop('disabled', true);
            $(btn).data('original-text', $(btn).html());
            $(btn).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + text);
        }

        // Reset tombol dari loading
        function resetButtonLoading(btn) {
            $(btn).prop('disabled', false);
            $(btn).html($(btn).data('original-text'));
        }

        // Proses transaksi
        async function prosesTransaksi(btn) {
            const totalHarga = keranjang.reduce((sum, item) => sum + item.subtotal, 0);
            setButtonLoading(btn, 'Menyimpan...');

            try {
                const response = await axios.post("{{ route('store-pesanan') }}", {
                    nama: 'PESANAN-' + Date.now(),
                    total: totalHarga,
                    metode_bayar: 'midtrans',
                    status_bayar: 'pending',
                    iduser: "{{ auth()->check() ? auth()->user()->id : '' }}",
                    items: keranjang,
                    _token: "{{ csrf_token() }}"
                });

                if (response.data.success) {
                    // Clear cart after successful order
                    keranjang = [];
                    renderKeranjang();

                    // Use Midtrans Snap JS to process payment
                    if (response.data.snap_token) {
                        // Call snap.pay with the token
                        snap.pay(response.data.snap_token, {
                            onSuccess: function (result) {
                                console.log('Midtrans onSuccess result:', result);
                                const qrImageUrl = `{{ url('/generate-qr') }}/${result.order_id}`;
                                updateStatusFromFrontend({
                                    order_id: result.order_id,
                                    status_bayar: 'success',
                                    metode_bayar: result.payment_type || 'midtrans'
                                }).then(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pembayaran Berhasil!',
                                        html: `
                                                    <p>Pesanan ID: <b>${result.order_id}</b></p>
                                                    <div id="swal-qr-container" class="text-center my-3">
                                                        <div class="spinner-border text-primary" role="status"></div>
                                                    </div>
                                                `,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Tutup',
                                        didOpen: () => {
                                            axios.get(`{{ url('/generate-qr') }}/${result.order_id}`)
                                                .then(response => {
                                                    $('#swal-qr-container').html(response.data);
                                                    $('#swal-qr-container svg').css({ 'width': '150px', 'height': '150px' });
                                                });
                                        }
                                    }).then(() => {
                                        refreshTransaksiTable();
                                    });
                                }).catch(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Pembayaran Berhasil!',
                                        html: `
                                                    <p>Pesanan ID: <b>${result.order_id}</b></p>
                                                    <div id="swal-qr-container" class="text-center my-3">
                                                        <div class="spinner-border text-primary" role="status"></div>
                                                    </div>
                                                `,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Tutup',
                                        didOpen: () => {
                                            axios.get(`{{ url('/generate-qr') }}/${result.order_id}`)
                                                .then(response => {
                                                    $('#swal-qr-container').html(response.data);
                                                    $('#swal-qr-container svg').css({ 'width': '150px', 'height': '150px' });
                                                });
                                        }
                                    }).then(() => {
                                        refreshTransaksiTable();
                                    });
                                });
                            },
                            onPending: function (result) {
                                console.log('Midtrans onPending result:', result);
                                updateStatusFromFrontend({
                                    order_id: result.order_id,
                                    status_bayar: 'pending',
                                    metode_bayar: 'midtrans'
                                }).finally(() => {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Pembayaran Pending',
                                        text: 'Transaksi ID: ' + result.order_id + '. Silakan selesaikan pembayaran.',
                                        timer: 2500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        refreshTransaksiTable();
                                    });
                                });
                            },
                            onError: function (result) {
                                console.log('Midtrans onError result:', result);
                                updateStatusFromFrontend({
                                    order_id: result.order_id,
                                    status_bayar: 'failed',
                                    metode_bayar: result.payment_type || 'midtrans'
                                }).finally(() => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Pembayaran Gagal',
                                        text: 'Terjadi kesalahan saat memproses pembayaran.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        refreshTransaksiTable();
                                    });
                                });
                            },
                            onClose: function () {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Pembayaran Dibatalkan',
                                    text: 'Anda menutup popup pembayaran. Status tetap pending.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    refreshTransaksiTable();
                                });
                            }
                        });
                    } else {
                        const message = response.data.message || 'Pesanan berhasil dibuat!';
                        Swal.fire('Berhasil!', message, 'success').then(() => refreshTransaksiTable());
                    }
                } else {
                    const message = response.data.message || 'Gagal membuat pesanan!';
                    Swal.fire('Gagal!', message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem', 'error');
            } finally {
                resetButtonLoading(btn);
            }
        }

        function updateStatusFromFrontend(payload) {
            console.log('Midtrans update-status disabled, payload:', payload);
            return Promise.resolve();
        }

        async function refreshTransaksiTable() {
            try {
                const response = await axios.get("{{ route('customer-list') }}", {
                    params: { ajax: 'transaksi' }
                });
                document.getElementById('transaksi-table-body').innerHTML = response.data;
            } catch (error) {
                console.error('Gagal memuat ulang tabel transaksi:', error);
            }
        }

        async function bayarPesanan(orderId, btn) {
            setButtonLoading(btn, 'Mempersiapkan...');

            try {
                const response = await axios.post("{{ route('midtrans.pay') }}", {
                    order_id: orderId,
                    _token: "{{ csrf_token() }}"
                });

                if (response.data.success && response.data.snap_token) {
                    snap.pay(response.data.snap_token, {
                        onSuccess: function (result) {
                            console.log('Midtrans onSuccess result:', result);
                            updateStatusFromFrontend({
                                order_id: result.order_id,
                                status_bayar: 'success',
                                metode_bayar: result.payment_type || 'midtrans'
                            }).then(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Berhasil!',
                                    text: 'Transaksi ID: ' + result.order_id + ' | Metode: ' + (result.payment_type || 'midtrans'),
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => {
                                    refreshTransaksiTable();
                                });
                            }).catch(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Berhasil!',
                                    text: 'Transaksi ID: ' + result.order_id,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    refreshTransaksiTable();
                                });
                            });
                        },
                        onPending: function (result) {
                            console.log('Midtrans onPending result:', result);
                            updateStatusFromFrontend({
                                order_id: result.order_id,
                                status_bayar: 'pending',
                                metode_bayar: 'midtrans'
                            }).finally(() => {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Pembayaran Pending',
                                    text: 'Transaksi ID: ' + result.order_id + '. Silakan selesaikan pembayaran.',
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => {
                                    refreshTransaksiTable();
                                });
                            });
                        },
                        onError: function (result) {
                            console.log('Midtrans onError result:', result);
                            updateStatusFromFrontend({
                                order_id: result.order_id,
                                status_bayar: 'failed',
                                metode_bayar: result.payment_type || 'midtrans'
                            }).finally(() => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran Gagal',
                                    text: 'Terjadi kesalahan saat memproses pembayaran.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    refreshTransaksiTable();
                                });
                            });
                        },
                        onClose: function () {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran Dibatalkan',
                                text: 'Anda menutup popup pembayaran. Status tetap pending.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                refreshTransaksiTable();
                            });
                        }
                    });
                } else {
                    Swal.fire('Gagal!', response.data.message || 'Gagal memulai pembayaran.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Gagal!', 'Terjadi kesalahan saat memproses pembayaran.', 'error');
            } finally {
                resetButtonLoading(btn);
            }
        }

        // Filter menu berdasarkan vendor
        function filterMenusByVendor(vendorId) {
            if (vendorId === 'all') {
                $('.menu-card').show();
            } else {
                $('.menu-card').each(function () {
                    const cardVendorId = $(this).data('vendor-id');
                    if (cardVendorId == vendorId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Hapus pesan kosong sebelumnya
            $('.empty-message').remove();

            // Cek apakah ada menu yang tampil
            const visibleCount = $('.menu-card:visible').length;
            if (visibleCount === 0) {
                $('#menu-cards-container').append('<div class="col-12 text-center py-4 empty-message"><p class="text-muted">Tidak ada menu dari vendor terpilih</p></div>');
            }
        }

        // Inisialisasi saat halaman dimuat
        $(document).ready(function () {
            // Filter menu berdasarkan vendor
            $('#vendor-filter').on('change', function () {
                filterMenusByVendor($(this).val());
            });

            // Tambah menu ke keranjang
            $(document).on('click', '.btn-tambah-keranjang', function () {
                const idmenu = $(this).data('idmenu');
                const nama_menu = $(this).data('nama');
                const harga = parseInt($(this).data('harga'));
                const nama_vendor = $(this).data('vendor');

                // Cek apakah item sudah ada di keranjang
                const existing = keranjang.find(item => item.idmenu === idmenu);

                if (existing) {
                    existing.qty += 1;
                    existing.subtotal = existing.qty * existing.harga;
                    Swal.fire({
                        icon: 'info',
                        title: 'Item Diperbarui!',
                        text: `Jumlah ${nama_menu} ditambah di keranjang.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    keranjang.push({
                        idmenu: idmenu,
                        nama_menu: nama_menu,
                        harga: harga,
                        qty: 1,
                        subtotal: harga,
                        nama_vendor: nama_vendor
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditambahkan!',
                        text: `${nama_menu} ditambahkan ke keranjang.`,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }

                renderKeranjang();
            });
        });

        function showQrModal(orderId, status) {
            // Cek apakah status sudah paid/success/settlement
            // Sesuaikan string status dengan yang ada di database kamu
            if (status !== 'success' && status !== 'paid' && status !== 'settlement') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Akses Ditolak',
                    text: 'QR Code hanya tersedia untuk pesanan yang sudah lunas.',
                    confirmButtonColor: '#3085d6'
                });
                return; // Berhenti di sini, modal tidak akan terbuka
            }

            const qrUrl = `{{ url('/generate-qr') }}/${orderId}`;

            // Tampilkan Loader
            $('#qrcode-container').html(`
            <div id="modal-loader" class="text-center p-4">
                <div class="spinner-border text-info" role="status"></div>
            </div>
        `);
            $('#qr-order-id').text('#' + orderId);

            // Tampilkan Modal
            $('#qrModal').modal('show');

            // Ambil SVG via AJAX
            axios.get(qrUrl)
                .then(response => {
                    setTimeout(() => {
                        $('#qrcode-container').html(response.data);
                        $('#qrcode-container svg').addClass('img-fluid');
                        // Optional: Beri styling agar SVG berada di tengah
                        $('#qrcode-container svg').css({
                            'display': 'block',
                            'margin': 'auto',
                            'width': '200px'
                        });
                    }, 300);
                })
                .catch(error => {
                    console.error(error);
                    $('#qrcode-container').html('<p class="text-danger">Gagal memuat QR</p>');
                });
        }
    </script>
@endpush