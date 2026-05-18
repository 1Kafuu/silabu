@extends('layouts.app')
@section('title', 'Edit Poli')
@section('page-title', 'Edit Poli')
@section('page-subtitle', 'Perbarui Data Poli')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Edit Poli</h4>
                        <p class="card-description">Perbarui data poli yang sudah ada</p>

                        <form id="update-poli-form" class="forms-sample" method="POST"
                            action="{{ route('update-poli', ['id' => $poli->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="name">Nama Poli</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Contoh: Poli Umum" value="{{ $poli->name }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="code">Kode Poli</label>
                                <input type="text" class="form-control" id="code" name="code"
                                    placeholder="Contoh: A, G, AN" value="{{ $poli->code }}" required>
                                <small class="form-text text-muted">Kode unik singkat untuk nomor antrian (maks. 10 karakter).</small>
                                @error('code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="form-check form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                            {{ $poli->is_active ? 'checked' : '' }}>
                                        Aktif
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" id="update-poli-button"
                                class="btn btn-gradient-primary me-2">Simpan</button>
                            <a href="{{ route('poli') }}" class="btn btn-light">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#update-poli-form').formHandler({
            submitButton: '#update-poli-button',
            rules: {
                name: { required: true },
                code: { required: true },
            },
            messages: {
                name: { required: 'Nama poli wajib diisi.' },
                code: { required: 'Kode poli wajib diisi.' },
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

        input.is-valid {
            border-color: #28a745 !important;
        }
    </style>
@endpush
