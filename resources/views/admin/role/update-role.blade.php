@extends('layouts.app')
@section('title', 'Update Role')
@section('page-title', 'Update Role')
@section('page-subtitle', 'Modify Role')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Roles</h4>
                        <p class="card-description">Edit existing Role</p>

                        <form id="update-role-form" class="forms-sample" method="POST" action="{{ route('update-role', ['id' => $role->idrole]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">Role Name</label>
                                    <input type="text" class="form-control" id="nama_role" name="nama_role" placeholder="Role Name"
                                        value="{{ $role->nama_role }}">
                                    @error('nama_role')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </form>
                            <button id="update-role-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('role') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#update-role-form').formHandler({
            submitButton: '#update-role-button',
            rules: {
                nama_role: { required: true },
            },
            messages: {
                nama_role: { required: "Please enter the role name" },
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