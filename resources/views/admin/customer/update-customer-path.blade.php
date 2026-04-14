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

                                <div id="camera-container" style="position: relative; display: inline-block;">
                                    <video id="video" width="320" height="240" autoplay
                                        style="border: 2px solid #ddd; border-radius: 8px;"></video>
                                    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>

                                    <img id="photo-preview" width="320" height="240"
                                        style="display:none; border: 2px solid #28a745; border-radius: 8px;">
                                </div>
                                <br>
                                <button type="button" class="btn btn-primary mt-2" id="snap">
                                    <i class="mdi mdi-camera"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning mt-2" id="retake" style="display:none;">
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
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const snap = document.getElementById('snap');
        const retake = document.getElementById('retake');
        const photoPreview = document.getElementById('photo-preview');
        const fotoInput = document.getElementById('foto_input');

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function (stream) {
                video.srcObject = stream;
                video.play();
            })
            .catch(function (err) {
                console.log("Gagal akses kamera: " + err);
                alert("Harap izinkan akses kamera pada browser Anda (HTTPS/localhost)");
            });

        snap.addEventListener('click', function () {
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, 320, 240);
            const dataUrl = canvas.toDataURL('image/png');

            
            fotoInput.value = dataUrl;

            video.style.display = 'none';
            snap.style.display = 'none';
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';
            retake.style.display = 'inline-block';
        });

        retake.addEventListener('click', function () {
            video.style.display = 'block';
            snap.style.display = 'inline-block';
            photoPreview.style.display = 'none';
            retake.style.display = 'none';
            fotoInput.value = ''; 
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