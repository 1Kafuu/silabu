@extends('layouts.app')
@section('title', 'Update items')
@section('page-title', 'Update items')
@section('page-subtitle', 'Update')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session(key: 'error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Items</h4>
                        <p class="card-description">Add new items</p>

                        @foreach ($barang as $row)
                            <form id="update-items-form" class="forms-sample" method="POST"
                                action="{{ route('update-items', ['id' => $row->id_barang]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="nama">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama"
                                        value="{{ $row->nama }}">
                                    @error('nama')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="harga">Harga</label>
                                    <input type="number" class="form-control" id="harga" name="harga" placeholder="Harga"
                                        value="{{ $row->harga }}">
                                    @error('harga')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </form>
                            <button id="update-submit-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('items-list') }}" class="btn btn-light">Cancel</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#update-items-form').formHandler({
            submitButton: '#update-submit-button',
            rules: {
                nama: { required: true },
                harga: { required: true },
            },
            messages: {
                nama: { required: "Please enter the items name" },
                harga: { required: "Please enter the items price" },
            },
            onSuccess: function (response, form) {
                if (response.success) {
                    sessionStorage.setItem('notification', response.notification);
                    window.location.href = response.redirect;
                }
            }
        });
    </script>
@endpush

@push('style-page')
    <style>
        .text-danger,
        .error {
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 10px;
            display: block;
            width: 100%;
        }

        input.is-invalid,
        select.is-invalid,
        textarea.is-invalid {
            border-color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }

        /* Style for valid fields */
        input.is-valid {
            border-color: #28a745 !important;
        }
    </style>
@endpush