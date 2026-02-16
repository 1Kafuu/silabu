@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'Users')
@section('page-subtitle', 'User Lists')

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
                        <h4 class="card-title mb-0">Users</h4>
                        <a href="{{ route('create-user') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-account-plus"></i>
                            <span class="mx-2">Add User</span>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Username</th>
                                    <th width="35%">Email</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $row)
                                    <tr>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            {{ $row->name }}
                                        </td>
                                        <td>
                                            {{ $row->email }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-end gap-2">
                                                <a href={{ route('edit-user', ['id' => $row->id]) }} class="btn btn-outline-success btn-sm">
                                                    <i c{lass="mdi mdi-account-edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('delete-user', ['id' => $row->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
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
    </div>
@endsection