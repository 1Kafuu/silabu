@extends('layouts.app')
@section('title', 'Create Items')
@section('page-title', 'Create Items')
@section('page-subtitle', 'Create')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Create Items</h4>
                        <p class="card-description">Add new items</p>
                        <form id="create-items-form" class="forms-sample" method="POST" action="{{ route('store-items') }}">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama">
                                @error('nama')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Harga">
                                @error('harga')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </form>
                        <button id="create-submit-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                        <a href="{{ route('items-list') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#create-items-form').formHandler({
            submitButton: '#create-submit-button',
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