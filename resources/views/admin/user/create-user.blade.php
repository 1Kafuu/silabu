@extends('layouts.app')
@section('title', 'Create User')
@section('page-title', 'Create User')
@section('page-subtitle', 'Add user')

@section('content')

    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Create Users</h4>
                        <p class="card-description">Add new users</p>
                        <form id="create-user-form" class="forms-sample" method="POST" action="{{ route('store-user') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Username</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Username"
                                    required>
                                @error('name')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                    required>
                                @error('email')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                                @error('password')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password-confirm">Confirm Password</label>
                                <input type="password" class="form-control" id="password-confirm"
                                    name="password_confirmation" placeholder="Password" required>
                                @error('password_confirmation')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </form>
                        <button type="submit" id="create-submit-button"
                            class="btn btn-gradient-primary me-2">Submit</button>
                        <a href="{{ route('user') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#create-user-form').formHandler({
            submitButton: '#create-submit-button',
            rules: {
                name: { required: true },
                email: { required: true, email: true },
                password: { required: true, minlength: 8 }
            },
            messages: {
                name: { required: "Please enter your username" },
                email: {
                    required: "We need your email address",
                    email: "Your email address must be in the format name@domain.com"
                },
                password: {
                    required: "Password is required",
                    minlength: "Password must be at least 8 characters long"
                }
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