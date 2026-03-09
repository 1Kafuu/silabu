@extends('layouts.app')
@section('title', 'Update User')
@section('page-title', 'Update User')
@section('page-subtitle', 'Modify User')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Users</h4>
                        <p class="card-description">Add new users</p>

                        @foreach ($user as $row)
                            <form  id="update-user-form" class="forms-sample" method="POST" action="{{ route('update-user', ['id' => $row->id]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">Username</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Username"
                                        value="{{ $row->name }}">
                                    @error('name')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                                        value="{{ $row->email }}" readonly>
                                    @error('email')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </form>
                            <button id="update-user-button" type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                            <a href="{{ route('user') }}" class="btn btn-light">Cancel</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#update-user-form').formHandler({
            submitButton: '#update-user-button',
            rules: {
                name: { required: true },
            },
            messages: {
                name: { required: "Please enter your username" },
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