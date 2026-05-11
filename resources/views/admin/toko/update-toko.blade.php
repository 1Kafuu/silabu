@extends('layouts.app')
@section('title', 'Update Toko')
@section('page-title', 'Toko')
@section('page-subtitle', 'Update Toko')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Toko</h4>
                        <p class="card-description">Edit data toko</p>

                        @foreach ($toko as $row)
                            <form id="update-toko-form" class="forms-sample" method="POST"
                                action="{{ route('update-toko', ['id' => $row->idtoko]) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="nama_toko">Nama Toko</label>
                                    <input type="text" class="form-control" id="nama_toko" name="nama_toko"
                                        placeholder="Nama Toko" value="{{ $row->nama_toko }}">
                                    @error('nama_toko')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude"
                                        placeholder="Latitude" value="{{ $row->latitude }}" readonly>
                                    @error('latitude')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="longtitude">Longitude</label>
                                    <input type="text" class="form-control" id="longtitude" name="longtitude"
                                        placeholder="Longitude" value="{{ $row->longtitude }}" readonly>
                                    @error('longtitude')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="accuracy">Accuracy (meter)</label>
                                    <input type="text" class="form-control" id="accuracy" name="accuracy"
                                        placeholder="Accuracy" value="{{ $row->accuracy }}" readonly>
                                    @error('accuracy')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <button type="button" id="btn-geolocation" class="btn btn-info">
                                        <span id="btn-geo-spinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                                        <i id="btn-geo-icon" class="mdi mdi-crosshairs-gps"></i>
                                        <span id="btn-geo-label"> Perbarui Lokasi</span>
                                    </button>
                                    <small id="geo-status" class="d-block mt-1 text-muted"></small>
                                </div>
                                <button id="update-submit-button" type="submit"
                                    class="btn btn-gradient-primary me-2">Simpan</button>
                                <a href="{{ route('toko-list') }}" class="btn btn-light">Batal</a>
                            </form>
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
        // Geolocation
        function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
            return new Promise((resolve, reject) => {
                let bestResult = null;
                const startTime = Date.now();

                const watchId = navigator.geolocation.watchPosition(
                    (position) => {
                        const acc = position.coords.accuracy;

                        if (!bestResult || acc < bestResult.coords.accuracy) {
                            bestResult = position;
                        }

                        if (acc <= targetAccuracy) {
                            navigator.geolocation.clearWatch(watchId);
                            resolve(bestResult);
                        }

                        if (Date.now() - startTime >= maxWait) {
                            navigator.geolocation.clearWatch(watchId);
                            if (bestResult) resolve(bestResult);
                            else reject(new Error("Timeout, tidak dapat posisi"));
                        }
                    },
                    (error) => reject(error),
                    { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
                );
            });
        }

        document.getElementById('btn-geolocation').addEventListener('click', async function () {
            const btn = document.getElementById('btn-geolocation');
            const label = document.getElementById('btn-geo-label');
            const status = document.getElementById('geo-status');
            const spinner = document.getElementById('btn-geo-spinner');
            const icon = document.getElementById('btn-geo-icon');

            btn.disabled = true;
            spinner.classList.remove('d-none');
            icon.classList.add('d-none');
            label.textContent = ' Mengambil lokasi...';
            status.textContent = 'Mohon tunggu, sedang mencari posisi terbaik...';
            status.className = 'd-block mt-1 text-muted';

            try {
                const pos = await getAccuratePosition(50, 20000);
                document.getElementById('latitude').value   = pos.coords.latitude;
                document.getElementById('longtitude').value = pos.coords.longitude;
                document.getElementById('accuracy').value   = pos.coords.accuracy;

                status.textContent = `Lokasi berhasil diperbarui (akurasi: ${pos.coords.accuracy.toFixed(1)} meter)`;
                status.className = 'd-block mt-1 text-success';
                label.textContent = ' Perbarui Lokasi';
            } catch (err) {
                status.textContent = 'Gagal mengambil lokasi: ' + err.message;
                status.className = 'd-block mt-1 text-danger';
                label.textContent = ' Perbarui Lokasi';
            } finally {
                btn.disabled = false;
                spinner.classList.add('d-none');
                icon.classList.remove('d-none');
            }
        });

        // Form handler
        $('#update-toko-form').formHandler({
            submitButton: '#update-submit-button',
            rules: {
                nama_toko:  { required: true },
                latitude:   { required: true },
                longtitude: { required: true },
                accuracy:   { required: true },
            },
            messages: {
                nama_toko:  { required: 'Nama toko wajib diisi' },
                latitude:   { required: 'Silakan ambil lokasi terlebih dahulu' },
                longtitude: { required: 'Silakan ambil lokasi terlebih dahulu' },
                accuracy:   { required: 'Silakan ambil lokasi terlebih dahulu' },
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
