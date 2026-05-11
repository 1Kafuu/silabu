@extends('layouts.app')

@section('title', 'Manajemen Toko')
@section('page-title', 'Toko')
@section('page-subtitle', 'Daftar Toko')

@section('content')
    <div id="notification-container"></div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Toko</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('create-toko') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-bookmark-plus"></i>
                                <span class="d-none d-sm-inline mx-1">Tambah Toko</span>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barcode</th>
                                    <th>Nama Toko</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Accuracy</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($toko as $row)
                                    <tr>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            <img src="data:image/png;base64,{{ $row->barcode_base64 }}" alt="Barcode">
                                        </td>
                                        <td>{{ $row->nama_toko }}</td>
                                        <td>{{ $row->latitude }}</td>
                                        <td>{{ $row->longtitude }}</td>
                                        <td>{{ $row->accuracy }}</td>
                                        <td>
                                            <div class="d-flex justify-end gap-2">
                                                <a href="{{ route('edit-toko', ['id' => $row->idtoko]) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-account-edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('delete-toko', ['id' => $row->idtoko]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                                        <i class="mdi mdi-account-remove"></i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </div>
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
        $(document).ready(function () {
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
