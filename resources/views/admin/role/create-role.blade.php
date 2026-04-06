@extends('layouts.app')
@section('title', 'Create Role')
@section('page-title', 'Create Role')
@section('page-subtitle', 'Add role')

@section('content')

    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Create Roles</h4>
                        <p class="card-description">Add new roles</p>
                        <form id="create-role-form" class="forms-sample" method="POST" action="{{ route('store-role') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Role Name</label>
                                <input type="text" class="form-control" id="nama_role" name="nama_role" placeholder="Role Name"
                                    required>
                                @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </form>
                        <button type="submit" id="create-submit-button"
                            class="btn btn-gradient-primary me-2">Submit</button>
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
        $('#create-role-form').formHandler({
            submitButton: '#create-submit-button',
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