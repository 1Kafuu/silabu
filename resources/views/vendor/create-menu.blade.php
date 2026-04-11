@extends('layouts.vendor')
@section('title', 'Create Menu')
@section('page-title', 'Create Menu')
@section('page-subtitle', 'Add menu')

@section('content')

    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Create Menu</h4>
                        <p class="card-description">Add new menu</p>
                        <form id="create-menu-form" class="forms-sample" method="POST" action="{{ route('store-menu') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="nama_menu">Menu Name</label>
                                <input type="text" class="form-control" id="nama_menu" name="nama_menu" placeholder="Menu Name"
                                    required>
                                @error('nama_menu')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="harga">Price</label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Price" min="0" step="0.01"
                                    required>
                                @error('harga')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="path_gambar">Image</label>
                                <input type="file" class="form-control" id="path_gambar" name="path_gambar" accept="image/*"
                                    required>
                                @error('path_gambar')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </form>
                        <button type="submit" id="create-submit-button"
                            class="btn btn-gradient-primary me-2">Submit</button>
                        <a href="{{ route('menu-list') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#create-menu-form').formHandler({
            submitButton: '#create-submit-button',
            rules: {
                nama_menu: { required: true },
                harga: { required: true, number: true, min: 0 },
                path_gambar: { required: true }
            },
            messages: {
                nama_menu: { required: "Please enter the menu name" },
                harga: { required: "Please enter the price", number: "Please enter a valid number", min: "Price must be at least 0" },
                path_gambar: { required: "Please select an image" }
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