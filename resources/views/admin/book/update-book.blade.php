@extends('layouts.app')
@section('title', 'Update Book')
@section('page-title', 'Update Book')
@section('page-subtitle', 'Update')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Book</h4>
                        <p class="card-description">Add new book</p>

                        @foreach ($buku as $r)
                            <form id="update-book-form" class="forms-sample" method="POST"
                                action="{{ route('update-book', ['id' => $r->idbuku]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="kode">Kode Buku</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Buku"
                                        value="{{ $r->kode }}" required>
                                    @error('kode')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="judul">Judul Buku</label>
                                    <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul Buku"
                                        value="{{ $r->judul }}" required>
                                    @error('judul')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pengarang">Pengarang</label>
                                    <input type="text" class="form-control" id="pengarang" name="pengarang"
                                        placeholder="Pengarang" value="{{ $r->pengarang }}" required>
                                    @error('pengarang')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="kategori">Kategori</label>
                                    <select name="idkategori" class="form-control form-select form-select-sm" id="kategori"
                                        required>
                                        <option value="">Pilih Kategori....</option>
                                        @foreach ($kategori as $row)
                                            <option value="{{ $row->idkategori }}" {{ ($r->idkategori ?? '') == $row->idkategori ? 'selected' : '' }}>
                                                {{ $row->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('idkategori')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </form>
                            <button id="update-submit-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('book-list') }}" class="btn btn-light">Cancel</a>
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
        $('#update-book-form').formHandler({
            submitButton: '#update-submit-button',
            rules: {
                kode: { required: true },
                judul: { required: true },
                pengarang: { required: true },
                kategori: { required: true }
            },
            messages: {
                kode: { required: "Please enter the book code" },
                judul: { required: "Please enter the book title" },
                pengarang: { required: "Please enter the author's name" },
                kategori: { required: "Please select a category" }
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