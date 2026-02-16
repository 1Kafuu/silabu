@extends('layouts.app')

@section('title', 'Book Management')
@section('page-title', 'Book')
@section('page-subtitle', 'Book Lists')

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
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Book</h4>
                        <a href="{{ route('create-book') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-book-plus"></i>
                            <span class="mx-2">Add Book</span>
                        </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th> No </th>
                                        <th> UID </th>
                                        <th> Title </th>
                                        <th> Author</th>
                                        <th> Category </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($book as $row)
                                    <tr>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            {{ $row->kode }}
                                        </td>
                                        <td>
                                            {{ $row->judul }}
                                        </td>
                                        <td>
                                            {{ $row->pengarang }}
                                        </td>
                                        <td>
                                            {{ $row->category->nama_kategori }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-end gap-2">
                                                <a href={{ route('edit-book', ['id' => $row->idbuku]) }} class="btn btn-outline-success btn-sm">
                                                    <i c{lass="mdi mdi-account-edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('delete-book', ['id' => $row->idbuku]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                                        <i class="mdi mdi-account-remove"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
@endsection