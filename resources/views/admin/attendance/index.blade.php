@extends('layouts.app')

@section('title', 'Absensi NFC')
@section('page-title', 'Absensi')
@section('page-subtitle', 'Scan Kartu NFC')

@section('content')
    <div class="row">

        {{-- Scanner Card --}}
        <div class="col-12 col-md-5 grid-margin">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title mb-1">Scan Kartu NFC</h4>
                    <p class="text-muted mb-4" style="font-size:0.85rem;">
                        Dekatkan kartu NFC mahasiswa ke perangkat, lalu tekan tombol Scan.
                    </p>

                    {{-- NFC visual indicator --}}
                    <div class="text-center my-3">
                        <div id="nfc-icon-wrapper" class="nfc-pulse-wrapper mx-auto">
                            <i class="mdi mdi-nfc-variant nfc-icon text-primary"></i>
                        </div>
                    </div>

                    {{-- Serial input --}}
                    <div class="form-group mt-3">
                        <label for="serial_number" class="font-weight-bold">Serial Number Kartu</label>
                        <input type="text" id="serial_number"
                            class="form-control form-control-lg text-center fs-6 font-weight-bold letter-spacing-1"
                            placeholder="Akan terisi otomatis" readonly>
                    </div>

                    {{-- Status --}}
                    <p id="nfc-status" class="text-muted text-center mt-2 mb-3"
                        style="min-height:1.4rem; font-size:0.9rem;">
                        Tekan tombol Scan NFC untuk memulai.
                    </p>

                    {{-- Scan button --}}
                    <button id="btn-scan-nfc" type="button" class="btn btn-primary btn-lg btn-block mt-auto">
                        <i class="mdi mdi-nfc"></i> Scan NFC
                    </button>

                    {{-- Submit button --}}
                    <button id="btn-submit" type="button" class="btn btn-success btn-lg btn-block mt-2" disabled>
                        <i class="mdi mdi-check-circle"></i> Catat Absensi
                    </button>
                </div>
            </div>
        </div>

        {{-- Result + Today's Log --}}
        <div class="col-12 col-md-7 grid-margin">
            {{-- Scan result alert --}}
            <div id="scan-result" class="mb-3" style="display:none;"></div>
            {{-- Today's attendance table --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Log Absensi Hari Ini</h4>
                        <span class="badge badge-primary badge-pill" id="attendance-count">
                            {{ $attendances->count() }}
                        </span>
                    </div>

                    @if ($attendances->isEmpty())
                        <div id="empty-state" class="text-center py-5">
                            <i class="mdi mdi-calendar-blank-outline" style="font-size:3rem; color:#c4c4c4;"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada absensi hari ini.</p>
                            <small class="text-muted">Data akan muncul setelah mahasiswa melakukan scan kartu NFC.</small>
                        </div>
                        <div class="table-responsive" id="table-wrapper" style="display:none;">
                    @else
                            <div class="table-responsive" id="table-wrapper">
                        @endif
                            <table class="table table-hover" id="attendance-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Nama</th>
                                        <th width="20%">NIM</th>
                                        <th width="20%">Waktu Scan</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-tbody">
                                    @forelse ($attendances as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->student->user->name ?? '-' }}</td>
                                            <td>{{ $row->student->NIM ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->scan_time)->format('H:i:s') }}</td>
                                            <td>
                                                <span class="badge badge-success">{{ ucfirst($row->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
@endsection

    @push('style-page')
        <link rel="stylesheet" href="{{ asset('css/pages/attendance.css') }}">
    @endpush

    @push('js-page')
        {{-- NFC Scanner config must come BEFORE the script --}}
        <script>
            window.NFCScannerConfig = {
                targetInput: '#serial_number',
                scanButton: '#btn-scan-nfc',
                statusEl: '#nfc-status',
                autoStop: true,

                onScan: function (serialNumber) {
                    // Enable submit once a card is read
                    document.getElementById('btn-submit').disabled = false;

                    // Pulse icon → success colour while waiting for submit
                    const wrapper = document.getElementById('nfc-icon-wrapper');
                    wrapper.classList.remove('scanning', 'error');
                    wrapper.classList.add('success');
                    wrapper.querySelector('.nfc-icon').classList.replace('text-primary', 'text-success');
                },

                onError: function (message) {
                    setIconState('error', 'text-danger');
                    document.getElementById('btn-submit').disabled = true;
                },
            };
        </script>
        <script src="{{ asset('js/nfc-scanner.js') }}"></script>

        <script>
            $(document).ready(function () {

                // Track scanning state for icon animation
                const $scanBtn = $('#btn-scan-nfc');
                const $wrapper = $('#nfc-icon-wrapper');
                const $icon = $wrapper.find('.mdi');

                // Observe button class changes to drive icon animation
                const observer = new MutationObserver(function () {
                    if ($scanBtn.hasClass('btn-warning')) {
                        $wrapper.addClass('scanning').removeClass('success error');
                        $icon.removeClass('text-success text-danger').addClass('text-primary');
                    }
                });
                observer.observe($scanBtn[0], { attributes: true, attributeFilter: ['class'] });

                // ── Submit attendance ──────────────────────────────────────
                $('#btn-submit').on('click', function () {
                    const serial = $('#serial_number').val().trim();
                    if (!serial) return;

                    const $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                    $.ajax({
                        url: '{{ route("attendance-scan") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            serial_number: serial,
                        },
                        success: function (res) {
                            showResult('success', res.message, res.student, res.nim, res.scan_time);
                            appendRow(res.student, res.nim, res.scan_time);
                            resetForm();
                        },
                        error: function (xhr) {
                            const res = xhr.responseJSON || {};
                            const msg = res.message || 'Terjadi kesalahan. Coba lagi.';
                            showResult('danger', msg, res.student || null, null, null);
                            resetForm();
                        },
                    });
                });

                // ── Helpers ────────────────────────────────────────────────
                function showResult(type, message, studentName, nim, scanTime) {
                    const iconMap = {
                        success: 'mdi-check-circle',
                        danger: 'mdi-alert-circle',
                        warning: 'mdi-alert',
                    };
                    const icon = iconMap[type] || 'mdi-information';

                    let extra = '';
                    if (studentName) {
                        extra += `<br><strong>Mahasiswa:</strong> ${studentName}`;
                    }
                    if (nim) {
                        extra += `&nbsp;&nbsp;<strong>NIM:</strong> ${nim}`;
                    }
                    if (scanTime) {
                        extra += `&nbsp;&nbsp;<strong>Waktu:</strong> ${scanTime}`;
                    }

                    $('#scan-result')
                        .html(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                            <i class="mdi ${icon} me-2"></i>${message}${extra}
                            <button type="button" class="btn-close" aria-label="Close"></button>
                       </div>`)
                        .show();

                    // Auto-dismiss after 6s then hide wrapper to remove gap
                    setTimeout(() => {
                        $('#scan-result .alert').fadeOut(300, function () {
                            $(this).remove();
                            $('#scan-result').hide();
                        });
                    }, 6000);

                    // Manual close button
                    $('#scan-result').off('click.alert').on('click.alert', '.btn-close, [data-bs-dismiss="alert"]', function () {
                        $(this).closest('.alert').fadeOut(300, function () {
                            $(this).remove();
                            $('#scan-result').hide();
                        });
                    });
                }

                function appendRow(name, nim, scanTime) {
                    // Hide empty state, show table on first entry
                    if ($('#empty-state').is(':visible')) {
                        $('#empty-state').hide();
                        $('#table-wrapper').show();
                    }

                    const count = $('#attendance-tbody tr').length + 1;
                    const row = `<tr class="new-row-flash">
                <td>${count}</td>
                <td>${name ?? '-'}</td>
                <td>${nim ?? '-'}</td>
                <td>${scanTime ?? '-'}</td>
                <td><span class="badge badge-success">Hadir</span></td>
            </tr>`;

                    $('#attendance-tbody').prepend(row);

                    // Update badge count
                    const current = parseInt($('#attendance-count').text()) || 0;
                    $('#attendance-count').text(current + 1);
                }

                function resetForm() {
                    $('#serial_number').val('');
                    $('#btn-submit').prop('disabled', true).html('<i class="mdi mdi-check-circle"></i> Catat Absensi');

                    const $wrapper = $('#nfc-icon-wrapper');
                    $wrapper.removeClass('scanning success error');
                    $wrapper.find('.mdi').removeClass('text-success text-danger').addClass('text-primary');
                }

            });
        </script>
    @endpush