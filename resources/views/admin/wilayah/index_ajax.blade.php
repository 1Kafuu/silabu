@extends('layouts.app')

@section('title', 'Wilayah Administrasi Indonesia (jQuery AJAX & API)')

@section('page-title', 'Wilayah Administrasi Indonesia')
@section('page-subtitle', 'City')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="card-title mb-0 text-white">Wilayah Administrasi Indonesia (jQuery AJAX & API)</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Provinsi:</label>
                            <select id="provinsi" class="form-select" onchange="getKotaAjax()">
                                <option value="0">Pilih Provinsi</option>
                                @foreach($provinsis as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kota/Kabupaten:</label>
                            <select id="kota" class="form-select" onchange="getKecamatanAjax()">
                                <option value="0">Pilih Kota</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <select id="kecamatan" class="form-select" onchange="getKelurahanAjax()">
                                <option value="0">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kelurahan:</label>
                            <select id="kelurahan" class="form-select" onchange="updateTextAjax()">
                                <option value="0">Pilih Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <small class="text-uppercase font-weight-bold text-muted">Lokasi Terpilih (Hasil AJAX):</small>
                    <h5 id="resultText" class="text-info mt-1">-</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js-page')
<script>
    function resetSelectAjax(ids) {
        ids.forEach(id => {
            $(`#${id}`).html(`<option value="0">Pilih ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`);
        });
    }

    function getKotaAjax() {
        const idProv = $('#provinsi').val();
        resetSelectAjax(['kota', 'kecamatan', 'kelurahan']);

        if (idProv == 0) return;

        $.ajax({
            url: "{{ route('get-kota') }}",
            type: "POST",
            data: {
                id: idProv,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                if(res.status === 'success') {
                    let options = '<option value="0">Pilih Kota</option>';
                    $.each(res.data, function(key, item) {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#kota').html(options);
                }
            },
            error: function(xhr) {
                console.error("Gagal mengambil kota:", xhr);
            }
        });
    }

    function getKecamatanAjax() {
        const idKota = $('#kota').val();
        resetSelectAjax(['kecamatan', 'kelurahan']);

        if (idKota == 0) return;

        $.ajax({
            url: "{{ route('get-kecamatan') }}",
            type: "POST",
            data: {
                id: idKota,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                let options = '<option value="0">Pilih Kecamatan</option>';
                $.each(res.data, function(key, item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#kecamatan').html(options);
            }
        });
    }

    function getKelurahanAjax() {
        const idKec = $('#kecamatan').val();
        resetSelectAjax(['kelurahan']);

        if (idKec == 0) return;

        $.ajax({
            url: "{{ route('get-kelurahan') }}",
            type: "POST",
            data: {
                id: idKec,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                let options = '<option value="0">Pilih Kelurahan</option>';
                $.each(res.data, function(key, item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                $('#kelurahan').html(options);
            }
        });
    }

    function updateTextAjax() {
        const prov = $('#provinsi option:selected').text();
        const kota = $('#kota option:selected').text();
        const kec = $('#kecamatan option:selected').text();
        const kel = $('#kelurahan option:selected').text();

        $('#resultText').text(`${kel}, ${kec}, ${kota}, ${prov}`);
    }
</script>
@endpush