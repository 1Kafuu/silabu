@extends('layouts.app')

@section('title', 'Add Student')
@section('page-title', 'Students')
@section('page-subtitle', 'Add Student')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div id="notification-container"></div>
                    <h4 class="card-title">Add Student</h4>
                    <p class="card-description">Fill in the student details below</p>
                    <form id="create-student-form" class="forms-sample" method="POST" action="{{ route('store-student') }}">
                        @csrf

                        <h6 class="mt-3 mb-3 text-muted form-section-title">Account Information</h6>

                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Password" required>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Confirm Password" required>
                                    @error('password_confirmation')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3 text-muted form-section-title">Student Information</h6>

                        <div class="form-group">
                            <label for="NIM">NIM</label>
                            <input type="text" class="form-control" id="NIM" name="NIM" placeholder="NIM (9 digits)"
                                maxlength="9" required>
                            @error('NIM')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="fakultas">Fakultas</label>
                                    <input type="text" class="form-control" id="fakultas" name="fakultas"
                                        placeholder="Fakultas" required>
                                    @error('fakultas')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="prodi">Program Studi</label>
                                    <input type="text" class="form-control" id="prodi" name="prodi"
                                        placeholder="Program Studi" required>
                                    @error('prodi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3 text-muted form-section-title">NFC Card</h6>

                        <div class="form-group">
                            <label for="serial_number">Serial Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="serial_number" name="serial_number"
                                    placeholder="Tap kartu NFC atau ketik manual...">
                                <button class="btn btn-primary" type="button" id="btn-scan-nfc">
                                    <i class="mdi mdi-nfc"></i>
                                    <span class="d-none d-sm-inline"> Scan NFC</span>
                                </button>
                            </div>
                            <small id="nfc-status" class="text-muted">Tekan tombol Scan NFC untuk memulai.</small>
                            @error('serial_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                            <button type="submit" id="create-submit-button" class="btn btn-gradient-primary">Submit</button>
                            <a href="{{ route('student-list') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script>
        window.NFCScannerConfig = {
            targetInput: '#serial_number',
            scanButton: '#btn-scan-nfc',
            statusEl: '#nfc-status',
            autoStop: true,
            onScan: function (serialNumber) {
                $('#serial_number').valid();
            },
            onError: function (message) {
                console.warn('[NFC]', message);
            }
        };
    </script>
    <script src="{{ asset('js/nfc-scanner.js') }}"></script>
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#create-student-form').formHandler({
                submitButton: '#create-submit-button',
                rules: {
                    name: { required: true },
                    email: { required: true, email: true },
                    password: { required: true, minlength: 8 },
                    password_confirmation: { required: true, equalTo: '#password' },
                    NIM: { required: true, minlength: 9, maxlength: 9, digits: true },
                    fakultas: { required: true },
                    prodi: { required: true },
                    serial_number: { required: true },
                },
                messages: {
                    name: { required: 'Please enter the full name' },
                    email: {
                        required: 'Email is required',
                        email: 'Please enter a valid email address'
                    },
                    password: {
                        required: 'Password is required',
                        minlength: 'Password must be at least 8 characters'
                    },
                    password_confirmation: {
                        required: 'Please confirm the password',
                        equalTo: 'Passwords do not match'
                    },
                    NIM: {
                        required: 'NIM is required',
                        minlength: 'NIM must be exactly 9 digits',
                        maxlength: 'NIM must be exactly 9 digits',
                        digits: 'NIM must contain numbers only',
                    },
                    fakultas: { required: 'Fakultas is required' },
                    prodi: { required: 'Program Studi is required' },
                    serial_number: { required: 'Please scan the NFC card' },
                },
                onSuccess: function (response, form) {
                    if (response.success) {
                        sessionStorage.setItem('notification', response.notification);
                        window.location.href = response.redirect;
                    }
                }
            });
        });
    </script>
@endpush

@push('style-page')
    <style>
        .form-section-title {
            border-bottom: 1px solid #e8e8e8;
            padding-bottom: 8px;
        }

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

        input.is-valid {
            border-color: #28a745 !important;
        }

        @media (max-width: 576px) {
            .input-group .btn {
                padding: 0.375rem 0.6rem;
            }
        }
    </style>
@endpush