@extends('layouts.app')
@section('title', 'Update Book')
@section('page-title', 'Update Book')
@section('page-subtitle', 'Update')

@section('content')
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
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Book</h4>
                        <p class="card-description">Add new book</p>

                        @foreach ($buku as $r)
                            <form class="forms-sample" method="POST" action="{{ route('update-book', ['id' => $r->idbuku]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="kode">Kode Buku</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Buku"
                                        value="{{ $r->kode }}">
                                    @error('kode')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="judul">Judul Buku</label>
                                    <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul Buku"
                                        value="{{ $r->judul }}">
                                    @error('judul')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="pengarang">Pengarang</label>
                                    <input type="text" class="form-control" id="pengarang" name="pengarang"
                                        placeholder="Pengarang" value="{{ $r->pengarang }}">
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
                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input"> Remember me
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                <a href="{{ route('book-list') }}" class="btn btn-light">Cancel</a>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection