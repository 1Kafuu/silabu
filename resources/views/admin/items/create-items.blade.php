@extends('layouts.app')
@section('title', 'Create Items')
@section('page-title', 'Create Items')
@section('page-subtitle', 'Create')

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
                        <h4 class="card-title">Create Items</h4>
                        <p class="card-description">Add new items</p>
                        <form class="forms-sample" method="POST" action="{{ route('store-items') }}">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    placeholder="Nama">
                                @error('nama')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                             <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga"
                                    placeholder="Harga">
                                @error('harga')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('items-list') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection