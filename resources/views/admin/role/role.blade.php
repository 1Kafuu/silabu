@extends('layouts.app')

@section('title', 'Role Management')
@section('page-title', 'Roles')
@section('page-subtitle', 'Role Lists')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Roles</h4>
                        <a href="{{ route('create-role') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-shield-plus"></i>
                            <span class="mx-2">Add Role</span>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">Role Name</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $row)
                                    <tr>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            {{ $row->nama_role }}
                                        </td>                                
                                        <td>
                                            <div class="d-flex justify-end gap-2">
                                                <a href={{ route('edit-role', ['id' => $row->idrole]) }}
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-account-edit"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('delete-role', ['id' => $row->idrole]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus role ini?')">
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

@push('js-page')
    <script>
        $(document).ready(function () {
            console.log('Document ready');
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');

                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endpush