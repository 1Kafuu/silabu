@extends('layouts.app')

@section('title', 'Customer Management (Blob)')
@section('page-title', 'Customers (Blob)')
@section('page-subtitle', 'Customer Lists')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Customer</h4>
                        <a href="{{ route('create-customerBlob') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-account-plus"></i>
                            <span class="mx-2">Add Customer</span>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Photo</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>Province</th>
                                    <th>City</th>
                                    <th>District</th>
                                    <th>Subdistrict</th>
                                    <th>Postal Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $row)
                                    <tr>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            @if($row->foto_blob)
                                                <img src="{{ is_resource($row->foto_blob) ? stream_get_contents($row->foto_blob): $row->foto_blob }}" width="100">
                                            @else
                                                No Photo
                                            @endif
                                        </td>
                                        <td>{{ $row->nama }}</td>
                                        <td>{{ $row->alamat }}</td>
                                        <td>{{ $row->provinsi }}</td>
                                        <td>{{ $row->kota }}</td>
                                        <td>{{ $row->kecamatan }}</td>
                                        <td>{{ $row->kelurahan }}</td>
                                        <td>{{ $row->kodepos }}</td>
                                        <td>
                                            <div class="d-flex justify-end gap-2">
                                                <a href={{ route('edit-customerBlob', ['id' => $row->idcustomer]) }}
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-account-edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('delete-customerBlob', ['id' => $row->idcustomer]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus customer ini?')">
                                                        <i class="mdi mdi-account-remove"></i>
                                                        <span>Delete</span>
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