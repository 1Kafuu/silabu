@extends('layouts.app')
@section('title', 'Create Vendor')
@section('page-title', 'Create Vendor')
@section('page-subtitle', 'Add vendor')

@section('content')

    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Create Vendor</h4>
                        <p class="card-description">Add new vendors</p>
                        <form id="create-vendor-form" class="forms-sample" method="POST" action="{{ route('store-vendor') }}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Vendor Name</label>
                                <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" placeholder="Vendor Name"
                                    required>
                                @error('nama_vendor')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="name">Owner</label>
                                <select class="form-select" id="iduser" name="iduser" required>
                                    <option value="">Select Owner</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('iduser')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </form>
                        <button type="submit" id="create-submit-button"
                            class="btn btn-gradient-primary me-2">Submit</button>
                        <a href="{{ route('vendor-list') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#create-vendor-form').formHandler({
            submitButton: '#create-submit-button',
            rules: {
                nama_vendor: { required: true },
            },
            messages: {
                nama_vendor: { required: "Please enter the vendor name" },
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