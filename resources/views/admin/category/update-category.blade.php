@extends('layouts.app')
@section('title', 'Update Category')
@section('page-title', 'Update Category')
@section('page-subtitle', 'Update')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Category</h4>
                        <p class="card-description">Add new category</p>

                        @foreach ($kategori as $row)
                            <form id="update-category-form" class="forms-sample" method="POST"
                                action="{{ route('update-category', ['id' => $row->idkategori]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="nama_kategori">Nama Kategori</label>
                                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                                        placeholder="Kategori" value="{{ $row->nama_kategori }}">
                                    @error('nama_kategori')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </form>
                            <button id="update-submit-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('category-list') }}" class="btn btn-light">Cancel</a>
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
        $('#update-category-form').formHandler({
            submitButton: '#update-submit-button',
            rules: {
                nama_kategori: { required: true },
            },
            messages: {
                nama_kategori: { required: "Please enter the category name" },
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