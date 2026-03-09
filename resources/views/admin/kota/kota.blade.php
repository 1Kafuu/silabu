@extends('layouts.app')

@section('title', 'City Arrangement')
@section('page-title', 'City')
@section('page-subtitle', 'City Input')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 mb-4">
            </div>

            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">Select</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tambah Kota Baru:</label>
                            <div class="input-group mb-3">
                                <input type="text" id="input-kota-biasa" class="form-control" placeholder="Nama Kota">
                                <button class="btn btn-primary" type="button" id="btn-add-biasa">Tambah</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kota (Native):</label>
                            <select id="select-biasa" class="form-select">
                                <option value="" disabled selected>-- Pilih Kota --</option>
                            </select>
                        </div>
                        <p class="mt-3">Kota Terpilih: <strong id="display-biasa">-</strong></p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">Select 2</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Tambah Kota Baru:</label>
                            <div class="input-group mb-3">
                                <input type="text" id="input-kota-s2" class="form-control" placeholder="Nama Kota">
                                <button class="btn btn-success" type="button" id="btn-add-s2">Tambah</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pilih Kota (Select2):</label>
                            <select id="select-s2" class="form-select">
                                <option value="" disabled selected>-- Pilih Kota --</option>
                            </select>
                        </div>
                        <p class="mt-3">Kota Terpilih: <strong id="display-s2">-</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-page')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            padding: 5px;
            border: 1px solid #ced4da;
        }

        .select2-container {
            width: 100% !important;
            display: block;
        }

        .card-custom {
            margin-bottom: 20px;
        }
    </style>
@endpush

@push('js-page')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#select-s2').select2({
            placeholder: "-- Pilih Kota --",
            allowClear: true
        });

        // 2. Logic untuk Card Select Biasa
        $('#btn-add-biasa').click(function () {
            let kota = $('#input-kota-biasa').val().trim();
            if (kota) {
                $('#select-biasa').append(`<option value="${kota}">${kota}</option>`);
                $('#input-kota-biasa').val('');
            }
        });

        $('#select-biasa').change(function () {
            $('#display-biasa').text($(this).val());
        });

        // 3. Logic untuk Card Select 2
        $('#btn-add-s2').click(function () {
            let kota = $('#input-kota-s2').val().trim();
            if (kota) {
                let newOption = `<option value="${kota}">${kota}</option>`;
                $('#select-s2').append(newOption).trigger('change');
                $('#input-kota-s2').val('');
            }
        });

        // Event change pada Select2
        $('#select-s2').on('change', function () {
            let data = $(this).val();
            $('#display-s2').text(data ? data : '-');
        });
    </script>
@endpush