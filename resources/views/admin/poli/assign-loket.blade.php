@extends('layouts.app')
@section('title', 'Assign Admin Loket')
@section('page-title', 'Poli Management')
@section('page-subtitle', 'Assign Admin Loket')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        {{-- Form Assign --}}
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Assign Admin Loket</h4>
                    <p class="card-description">Hubungkan user ke poli sebagai Admin Loket</p>

                    @if (!$roleAdminLoket)
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert"></i>
                            Role <strong>Admin Loket</strong> belum ada. Buat terlebih dahulu di
                            <a href="{{ route('create-role') }}">Role Management</a>.
                        </div>
                    @else
                        <form id="assign-loket-form" class="forms-sample" method="POST"
                            action="{{ route('poli.assign-loket.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="iduser">Pilih User</label>
                                <select class="form form-select" id="iduser" name="iduser" required>
                                    <option value="" disabled selected>-- Pilih User --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} — {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('iduser')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Pilih Poli <span class="text-muted" style="font-weight:400;font-size:0.82rem;">(bisa pilih lebih dari satu)</span></label>
                                <div class="poli-checkbox-list" id="poli-checkbox-list">
                                    @foreach ($polis as $poli)
                                        <label class="poli-checkbox-item">
                                            <input type="checkbox" name="poli_ids[]" value="{{ $poli->id }}">
                                            <span class="poli-checkbox-label">
                                                <span class="badge badge-outline-primary me-1">{{ $poli->code }}</span>
                                                {{ $poli->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="poli-checkbox-actions mt-2">
                                    <button type="button" id="check-all" class="btn btn-outline-secondary btn-xs">Pilih Semua</button>
                                    <button type="button" id="uncheck-all" class="btn btn-outline-secondary btn-xs">Hapus Semua</button>
                                </div>
                                @error('poli_ids')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div id="poli-ids-error" class="text-danger" style="display:none;">Pilih minimal satu poli.</div>
                            </div>

                            <button type="submit" id="assign-loket-button"
                                class="btn btn-gradient-primary me-2">
                                <i class="mdi mdi-account-plus"></i>
                                <span class="mx-1">Assign</span>
                            </button>
                            <a href="{{ route('poli') }}" class="btn btn-light">Batal</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabel Assignment --}}
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Daftar Assignment</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">User</th>
                                    <th width="55%">Poli & Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grouped = $assignments->groupBy(fn($r) => $r->iduser);
                                @endphp
                                @forelse ($grouped as $userId => $rows)
                                    @php $user = $rows->first()->user; @endphp
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $user?->name ?? '—' }}</div>
                                            <small class="text-muted">{{ $user?->email }}</small>
                                        </td>
                                        <td>
                                            <div class="poli-assignment-list">
                                                @foreach ($rows as $row)
                                                    <div class="poli-assignment-item">
                                                        <span class="poli-assignment-badge">
                                                            @if ($row->poli)
                                                                <span class="badge badge-outline-primary">{{ $row->poli->code }}</span>
                                                                <span class="poli-assignment-name">{{ $row->poli->name }}</span>
                                                            @else
                                                                <span class="text-muted">Semua Poli</span>
                                                            @endif
                                                        </span>
                                                        <form method="POST" class="d-inline"
                                                            action="{{ route('poli.assign-loket.destroy', $row->idrole_user) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-icon-sm"
                                                                title="Hapus assignment {{ $row->poli?->name }}"
                                                                onclick="return confirm('Hapus assignment {{ $row->poli?->name }}?')">
                                                                <i class="mdi mdi-close"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="mdi mdi-account-off"
                                                style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                            Belum ada assignment Admin Loket.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Show persisted notification
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');
                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () { $(this).remove(); });
                }, 5000);
            }

            // Select all / deselect all
            $('#check-all').on('click', function () {
                $('#poli-checkbox-list input[type="checkbox"]').prop('checked', true);
                updateCheckboxStyles();
            });
            $('#uncheck-all').on('click', function () {
                $('#poli-checkbox-list input[type="checkbox"]').prop('checked', false);
                updateCheckboxStyles();
            });

            // Visual feedback on checkbox change
            $('#poli-checkbox-list').on('change', 'input[type="checkbox"]', function () {
                updateCheckboxStyles();
                $('#poli-ids-error').hide();
            });

            function updateCheckboxStyles() {
                $('#poli-checkbox-list .poli-checkbox-item').each(function () {
                    const checked = $(this).find('input').is(':checked');
                    $(this).toggleClass('is-checked', checked);
                });
            }
        });

        $('#assign-loket-form').on('submit', function (e) {
            e.preventDefault();

            // Manual validation for checkboxes
            const checked = $('#poli-checkbox-list input[type="checkbox"]:checked').length;
            if (checked === 0) {
                $('#poli-ids-error').show();
                return;
            }
            $('#poli-ids-error').hide();

            const form   = $(this);
            const btn    = $('#assign-loket-button');
            const url    = form.attr('action');
            const data   = form.serialize();

            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

            $.ajax({
                url:  url,
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.success) {
                        sessionStorage.setItem('notification', response.notification);
                        window.location.href = response.redirect;
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-account-plus"></i> <span class="mx-1">Assign</span>');
                    if (xhr.responseJSON && xhr.responseJSON.notification) {
                        $('#notification-container').html(xhr.responseJSON.notification);
                    }
                }
            });
        });
    </script>
@endpush

@push('style-page')
    <style>
        .text-danger, .error {
            color: #dc3545 !important;
            font-size: 0.875rem;
            margin-top: 6px;
            display: block;
            width: 100%;
        }
        select.is-invalid {
            border-color: #dc3545 !important;
            border: 2px solid #dc3545 !important;
        }

        /* Poli checkbox list */
        .poli-checkbox-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            max-height: 260px;
            overflow-y: auto;
            border: 1px solid #e8eaf6;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fafbff;
        }
        .poli-checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
            margin: 0;
            font-weight: 400;
            border: 1.5px solid transparent;
        }
        .poli-checkbox-item:hover {
            background: #f3e8ff;
        }
        .poli-checkbox-item.is-checked {
            background: #ede9fe;
            border-color: #c084fc;
        }
        .poli-checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #7e22ce;
            flex-shrink: 0;
            cursor: pointer;
        }
        .poli-checkbox-label {
            font-size: 0.9rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .poli-checkbox-actions {
            display: flex;
            gap: 8px;
        }
        .btn-xs {
            padding: 3px 10px;
            font-size: 0.78rem;
        }

        /* Scrollbar for checkbox list */
        .poli-checkbox-list::-webkit-scrollbar { width: 4px; }
        .poli-checkbox-list::-webkit-scrollbar-track { background: transparent; }
        .poli-checkbox-list::-webkit-scrollbar-thumb { background: #d8b4fe; border-radius: 4px; }

        /* Poli assignment grouped list */
        .poli-assignment-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .poli-assignment-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 5px 8px;
            border-radius: 6px;
            background: #fafbff;
            border: 1px solid #ede9fe;
            transition: background 0.15s;
        }
        .poli-assignment-item:hover {
            background: #f3e8ff;
        }
        .poli-assignment-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
        }
        .poli-assignment-name {
            font-size: 0.85rem;
            color: #444;
        }
        .btn-icon-sm {
            padding: 2px 6px;
            font-size: 0.75rem;
            line-height: 1.4;
            border-radius: 5px;
            flex-shrink: 0;
        }
    </style>
@endpush
