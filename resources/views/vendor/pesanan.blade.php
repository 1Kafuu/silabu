@extends('layouts.vendor')

@section('title', 'Pesanan Management')
@section('page-title', 'Pesanan')
@section('page-subtitle', 'Pesanan Lists')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Pesanan</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="8%">No</th>
                                    <th width="18%">Nama Pelanggan</th>
                                    <th width="24%">Menu</th>
                                    <th width="10%">Qty</th>
                                    <th width="14%">Price</th>
                                    <th width="14%">Subtotal</th>
                                    <th width="14%">Status Bayar</th>
                                    <th width="12%">Metode Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $row)
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $row->nama_pemesan }}</td>
                                        <td>{{ $row->nama_menu }}</td>
                                        <td>{{ $row->jumlah }}</td>
                                        <td>{{ Illuminate\Support\Number::currency($row->harga, 'IDR', 'id') }}</td>
                                        <td>{{ Illuminate\Support\Number::currency($row->subtotal, 'IDR', 'id') }}</td>
                                        <td>
                                            @if ($row->status_bayar === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif ($row->status_bayar === 'success')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif ($row->status_bayar === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($row->status_bayar) }}</span>
                                            @endif
                                        </td>  
                                        <td>{{ ucfirst($row->metode_bayar) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada pesanan sukses untuk vendor ini.</td>
                                    </tr>
                                @endforelse
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
        $(document).ready(function () {
            console.log('Document ready');
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');

                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endpush