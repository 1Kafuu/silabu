@extends('layouts.form')
@section('title', 'Update Category')

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
            <div class="col-12 col-sm-4 stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Category</h4>
                        <p class="card-description">Add new category</p>

                        @foreach ($kategori as $row)
                            <form class="forms-sample" method="POST"
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
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                <a href="{{ route('category-list') }}" class="btn btn-light">Cancel</a>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection