@extends('layouts.app')
@section('title', 'Manage User Roles')
@section('page-title', 'Manage User Roles')
@section('page-subtitle', 'Assign roles to user')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Manage Roles for: {{ $user->name }}</h4>
                        <a href="{{ route('user') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-arrow-left"></i>
                            <span class="mx-2">Back to Users</span>
                        </a>
                    </div>
                    <p class="text-muted mb-4">User can have multiple roles, but only one can be active at a time</p>

                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <form id="manage-role-form" method="POST"
                                        action="{{ route('update-roles', ['id' => $user->id]) }}">
                                        @csrf
                                        @method('POST')
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="table bg-light">
                                                    <tr>
                                                        <th>Nama Role</th>
                                                        <th class="text-center">Pilih</th>
                                                        <th class="text-center">Role Aktif</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($roles as $role)
                                                        @php
                                                            $hasRole = $userRoles->where('idrole', $role->idrole)->first();
                                                            $isActive = $hasRole && $hasRole->status == 1;
                                                        @endphp
                                                        <tr>
                                                            <td class="align-middle">{{ $role->nama_role }}</td>
                                                            <td class="align-middle text-center">
                                                                <input type="checkbox" name="roles[]" value="{{ $role->idrole }}"
                                                                    {{ $hasRole ? 'checked' : '' }}>
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                <input type="radio" name="active_role" value="{{ $role->idrole }}"
                                                                    {{ $isActive ? 'checked' : '' }}
                                                                    {{ !$hasRole ? 'disabled' : '' }}>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-gradient-primary mt-3">
                                            <i class="mdi mdi-content-save"></i>
                                            <span class="mx-2">Simpan Perubahan</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        // Ketika checkbox dipilih, radio juga aktif
        $('input[name="roles[]"]').on('change', function() {
            var radio = $(this).closest('tr').find('input[name="active_role"]');
            if ($(this).is(':checked')) {
                radio.prop('disabled', false);
            } else {
                radio.prop('disabled', true);
                radio.prop('checked', false);
            }
        });

        $('#manage-role-form').formHandler({
            submitButton: 'button[type="submit"]',
            rules: {
                'roles[]': { required: true }
            },
            messages: {
                'roles[]': { required: "Please select at least one role" }
            },
            onSuccess: function (response, form) {
                if (response.success) {
                    sessionStorage.setItem('notification', response.notification);
                    window.location.href = response.redirect;
                }
            },
            onError: function (xhr, form) {
                if (xhr.responseJSON && xhr.responseJSON.success === false) {
                    sessionStorage.setItem('notification', xhr.responseJSON.notification);
                    window.location.href = window.location.href;
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