@extends('layouts.app')

@section('title', 'Edit Student')
@section('page-title', 'Students')
@section('page-subtitle', 'Edit Student')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div id="notification-container"></div>
                    <h4 class="card-title">Edit Student</h4>
                    <p class="card-description">Only NIM, Fakultas, and Prodi can be updated</p>

                    <form id="edit-student-form" class="forms-sample" method="POST"
                        action="{{ route('update-student', ['id' => $student->id]) }}">
                        @csrf
                        @method('PUT')

                        <h6 class="mt-3 mb-3 text-muted form-section-title">Account Information</h6>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control"
                                        value="{{ $student->user->name ?? '-' }}" disabled>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control"
                                        value="{{ $student->user->email ?? '-' }}" disabled>
                                </div>
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3 text-muted form-section-title">Student Information</h6>

                        <div class="form-group">
                            <label for="NIM">NIM</label>
                            <input type="text" class="form-control" id="NIM" name="NIM"
                                value="{{ $student->NIM }}" maxlength="9" required>
                            @error('NIM')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="fakultas">Fakultas</label>
                                    <input type="text" class="form-control" id="fakultas" name="fakultas"
                                        value="{{ $student->fakultas }}" required>
                                    @error('fakultas')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="prodi">Program Studi</label>
                                    <input type="text" class="form-control" id="prodi" name="prodi"
                                        value="{{ $student->prodi }}" required>
                                    @error('prodi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                            <button type="submit" id="edit-submit-button"
                                class="btn btn-gradient-primary">Update</button>
                            <a href="{{ route('student-list') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#edit-student-form').formHandler({
            submitButton: '#edit-submit-button',
            rules: {
                NIM: { required: true, minlength: 9, maxlength: 9, digits: true },
                fakultas: { required: true },
                prodi: { required: true },
            },
            messages: {
                NIM: {
                    required: 'NIM is required',
                    minlength: 'NIM must be exactly 9 digits',
                    maxlength: 'NIM must be exactly 9 digits',
                    digits: 'NIM must contain numbers only',
                },
                fakultas: { required: 'Fakultas is required' },
                prodi: { required: 'Program Studi is required' },
            },
            onSuccess: function(response, form) {
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

        input:disabled {
            background-color: #f4f4f4 !important;
            cursor: not-allowed;
        }
    </style>
@endpush
