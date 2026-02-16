@extends('layouts.form')
@section('title', 'Create Book')

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
            <div class="col-12 col-sm-4 stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Book</h4>
                        <p class="card-description">Add new book</p>
                        <form class="forms-sample" method="POST" action="{{ route('store-book') }}">
                            @csrf
                            <div class="form-group">
                                <label for="kode">Kode Buku</label>
                                <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Buku">
                                @error('kode')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="judul">Judul Buku</label>
                                <input type="text" class="form-control" id="judul" name="judul" placeholder="Judul Buku">
                                @error('judul')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="pengarang">Pengarang</label>
                                <input type="text" class="form-control" id="pengarang" name="pengarang"
                                    placeholder="Pengarang">
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
                                        <option value="{{ $row->idkategori }}">{{ $row->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                @error('kategori')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('book-list') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection