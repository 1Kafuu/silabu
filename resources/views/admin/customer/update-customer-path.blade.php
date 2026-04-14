@extends('layouts.app')
@section('title', 'Update Customer')
@section('page-title', 'Update Customer')
@section('page-subtitle', 'Modify customer')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <h4 class="card-title">Update Customer</h4>
                        <p class="card-description">Edit existing customer</p>

                        <form id="update-customer-form" class="forms-sample" method="POST"
                            action="{{ route('update-customerPath', ['id' => $customer->idcustomer]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="nama">Customer Name</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Customer Name"
                                    value="{{ $customer->nama }}">
                                @error('nama')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="alamat">Address</label>
                                <textarea class="form-control" id="alamat" name="alamat" placeholder="Address"
                                    required>{{ $customer->alamat }}</textarea>
                                @error('alamat')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="provinsi">Province</label>
                                <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Province"
                                    value="{{ $customer->provinsi }}">
                                @error('provinsi')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kota">City</label>
                                <input type="text" class="form-control" id="kota" name="kota" placeholder="City"
                                    value="{{ $customer->kota }}">
                                @error('kota')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kecamatan">District</label>
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan"
                                    placeholder="District" value="{{ $customer->kecamatan }}">
                                @error('kecamatan')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kelurahan">Subdistrict</label>
                                <input type="text" class="form-control" id="kelurahan" name="kelurahan"
                                    placeholder="Subdistrict" value="{{ $customer->kelurahan }}">
                                @error('kelurahan')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="kodepos">Postal Code</label>
                                <input type="number" class="form-control" id="kodepos" name="kodepos"
                                    placeholder="Postal Code" value="{{ $customer->kodepos }}">
                                @error('kodepos')
                                    <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                            <div class="form-group text-center">
                                <label>Ambil Foto</label><br>

                                <!-- Preview yang muncul di form -->
                                <div id="photo-preview-container" class="mb-3"
                                    style="border: 2px dashed #ccc; padding: 10px; border-radius: 8px; min-height: 100px;">
                                    @if($customer->foto_path)
                                        <img id="existing-photo-preview" src="{{ asset('storage/' . $customer->foto_path) }}"
                                            class="img-fluid"
                                            style="display: block; max-width: 320px; max-height: 240px; border: 2px solid #28a745; border-radius: 8px;" />
                                    @else
                                        <p class="text-muted mb-0" id="no-photo-text">Belum ada foto</p>
                                        <img id="photo-preview" class="img-fluid"
                                            style="display:none; max-width: 320px; max-height: 240px; border-radius: 8px;" />
                                    @endif
                                </div>

                                <button type="button" class="btn btn-primary mt-2" id="open-camera-btn">
                                    <i class="mdi mdi-camera"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning mt-2" id="retake-btn" style="display:none;">
                                    <i class="mdi mdi-refresh"></i> Foto Ulang
                                </button>

                                <input type="hidden" name="foto_path" id="foto_input">
                            </div>
                        </form>
                        <button id="update-customer-button" type="submit"
                            class="btn btn-gradient-primary me-2">Submit</button>
                        <a href="{{ route('manage-customerPath') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kamera -->
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraModalLabel">Ambil Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2">Live Preview</h6>
                            <div style="position: relative;">
                                <video id="modal-video" class="w-100 h-auto" autoplay
                                    style="border: 2px solid #ddd; border-radius: 8px; background: #000;"></video>
                                <button type="button" class="btn btn-danger btn-sm mt-2 w-100" id="close-camera-btn">
                                    <i class="mdi mdi-close"></i> Tutup
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2">Snapshot</h6>
                            <canvas id="modal-canvas" width="640" height="480" style="display:none;"></canvas>
                            <img id="modal-photo-preview" class="img-fluid w-100 border-success"
                                style="display: none; border: 2px solid; border-radius: 8px;" />

                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-primary flex-fill" id="snap-modal-btn">
                                    <i class="mdi mdi-camera"></i> Ambil
                                </button>
                                <button type="button" class="btn btn-warning flex-fill" id="retake-modal-btn"
                                    style="display: none;">
                                    <i class="mdi mdi-refresh"></i> Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="save-image-btn" disabled>
                        <i class="mdi mdi-content-save"></i> Simpan Gambar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script src="{{ asset('js/jquery-form-handler.js') }}"></script>
    <script>
        $('#update-customer-form').formHandler({
            submitButton: '#update-customer-button',
            rules: {
                nama: { required: true },
                alamat: { required: true },
                provinsi: { required: true },
                kota: { required: true },
                kecamatan: { required: true },
                kelurahan: { required: true },
                kodepos: { required: true },
            },
            messages: {
                nama: { required: "Please enter the customer name" },
                alamat: { required: "Please enter the address" },
                provinsi: { required: "Please enter the province" },
                kota: { required: "Please enter the city" },
                kecamatan: { required: "Please enter the district" },
                kelurahan: { required: "Please enter the subdistrict" },
                kodepos: { required: "Please enter the postal code" },
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

@push('js-page')
    <script>
        // Variabel untuk Modal
        const modalVideo = document.getElementById('modal-video');
        const modalCanvas = document.getElementById('modal-canvas');
        const modalPhotoPreview = document.getElementById('modal-photo-preview');
        const snapModalBtn = document.getElementById('snap-modal-btn');
        const retakeModalBtn = document.getElementById('retake-modal-btn');
        const saveImageBtn = document.getElementById('save-image-btn');

        // Variabel untuk Form
        const openCameraBtn = document.getElementById('open-camera-btn');
        const retakeBtn = document.getElementById('retake-btn');
        const photoPreview = document.getElementById('photo-preview');
        const noPhotoText = document.getElementById('no-photo-text');
        const fotoInput = document.getElementById('foto_input');

        let stream = null;
        let modalInstance = null;

        // Buka Modal Kamera
        openCameraBtn.addEventListener('click', function () {
            if (stream) {
                // Kamera sudah aktif, langsung tampilkan
            } else {
                // Mulai kamera
                navigator.mediaDevices.getUserMedia({ video: true, audio: false })
                    .then(function (s) {
                        stream = s;
                        modalVideo.srcObject = stream;
                        modalVideo.play();
                    })
                    .catch(function (err) {
                        console.log("Gagal akses kamera: " + err);
                        alert("Harap izinkan akses kamera pada browser Anda (HTTPS/localhost)");
                    });
            }

            // Reset state untuk ambil foto baru
            modalPhotoPreview.style.display = 'none';
            snapModalBtn.style.display = 'inline-block';
            retakeModalBtn.style.display = 'none';
            saveImageBtn.disabled = true;

            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('cameraModal'));
            modal.show();
            modalInstance = modal;
        });

        // Ambil foto di dalam modal
        snapModalBtn.addEventListener('click', function () {
            const context = modalCanvas.getContext('2d');

            // Set resolusi canvas sesuai dengan resolusi asli video stream
            modalCanvas.width = modalVideo.videoWidth;
            modalCanvas.height = modalVideo.videoHeight;

            // Gambar ke canvas
            context.drawImage(modalVideo, 0, 0, modalCanvas.width, modalCanvas.height);

            const dataUrl = modalCanvas.toDataURL('image/png');

            modalPhotoPreview.src = dataUrl;
            modalPhotoPreview.style.display = 'block';

            snapModalBtn.style.display = 'none';
            retakeModalBtn.style.display = 'inline-block';
            saveImageBtn.disabled = false;
        });

        // Foto ulang
        retakeModalBtn.addEventListener('click', function () {
            modalPhotoPreview.style.display = 'none';
            modalCanvas.style.display = 'none';
            snapModalBtn.style.display = 'inline-block';
            retakeModalBtn.style.display = 'none';
            saveImageBtn.disabled = true;
        });

        // Simpan gambar ke form
        saveImageBtn.addEventListener('click', function () {
            const dataUrl = modalPhotoPreview.src;
            fotoInput.value = dataUrl;

            // Update preview di form
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';
            noPhotoText.style.display = 'none';

            // Reset tombol di form
            openCameraBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-block';

            // Tutup modal
            if (modalInstance) {
                modalInstance.hide();
            }
        });

        // Foto ulang di form
        retakeBtn.addEventListener('click', function () {
            // Reset preview di form
            photoPreview.style.display = 'none';
            noPhotoText.style.display = 'block';
            fotoInput.value = '';

            // Reset tombol di form
            openCameraBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
        });

        // Tutup kamera saat modal ditutup
        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function () {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            if (modalVideo) {
                modalVideo.srcObject = null;
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