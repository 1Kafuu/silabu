@extends('layouts.app')
@section('title', 'Update Menu')
@section('page-title', 'Update Menu')
@section('page-subtitle', 'Modify Menu')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Menu</h4>
                        <p class="card-description">Edit existing Menu</p>

                        <form id="update-menu-form" class="forms-sample" method="POST"
                            action="{{ route('update-menu', ['id' => $menu->idmenu]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Menu Name</label>
                                <input type="text" class="form-control" id="nama_menu" name="nama_menu"
                                    placeholder="Menu Name" value="{{ $menu->nama_menu }}" required>
                                @error('nama_menu')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="harga">Price</label>
                                <input type="number" class="form-control" id="harga" name="harga"
                                    placeholder="Price" value="{{ $menu->harga }}" min="0" step="0.01" required>
                                @error('harga')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="path_gambar">Image</label>
                                <input type="file" class="form-control" id="path_gambar" name="path_gambar"
                                    accept="image/*">
                                @error('path_gambar')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                                @if($menu->path_gambar)
                                    <small class="form-text text-muted">
                                        <br>
                                        <img src="{{ asset('storage/' . $menu->path_gambar) }}" alt="Current Menu Image" style="max-width: 150px; max-height: 150px;">
                                    </small>
                                @endif
                            </div>
                        </form>
                        <button id="update-menu-button" type="submit"
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script>
        $('#update-menu-form').formHandler({
            submitButton: '#update-menu-button',
            rules: {
                nama_menu: { required: true },
                harga: { required: true, number: true, min: 0 },
                path_gambar: { required: false, extension: "jpg|jpeg|png|gif" }
            },
            messages: {
                nama_menu: { required: "Please enter the menu name" },
                harga: { required: "Please enter the price", number: "Please enter a valid number", min: "Price must be at least 0" }
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
