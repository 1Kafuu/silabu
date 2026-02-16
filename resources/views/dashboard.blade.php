@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview')

@section('content')
    <div class="row">
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white">
                <div class="card-body">
                    <img src="{{asset('images/dashboard/circle.svg')}}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Users Online<i class="mdi mdi-account mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $user }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Book<i class="mdi mdi-book mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $book }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white">
                <div class="card-body">
                    <img src="{{ asset('images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">Category<i class="mdi mdi-bookmark mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $category }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Book Entries</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> Code </th>
                                    <th> Title </th>
                                    <th> Author </th>
                                    <th> Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($buku as $row)
                                <tr>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection