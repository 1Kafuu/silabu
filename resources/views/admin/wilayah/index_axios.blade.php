@extends('layouts.app')

@section('title', 'Wilayah Administrasi Indonesia (Axios & API)')

@section('page-title', 'Wilayah Administrasi Indonesia')
@section('page-subtitle', 'City')

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="card-title mb-0 text-white">Wilayah Administrasi Indonesia (Axios & API)</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Provinsi:</label>
                            <select id="provinsi" class="form-select" onchange="getKota()">
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
                            <select id="kota" class="form-select" onchange="getKecamatan()">
                                <option value="0">Pilih Kota</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <select id="kecamatan" class="form-select" onchange="getKelurahan()">
                                <option value="0">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kelurahan:</label>
                            <select id="kelurahan" class="form-select" onchange="updateText()">
                                <option value="0">Pilih Kelurahan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border">
                    <small class="text-uppercase font-weight-bold text-muted">Lokasi Terpilih:</small>
                    <h5 id="resultText" class="text-primary mt-1">-</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    // Fungsi bantuan untuk reset dropdown
    function resetSelect(ids) {
        ids.forEach(id => {
            const el = document.getElementById(id);
            el.innerHTML = `<option value="0">Pilih ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`;
        });
    }

    // Fungsi utama mengambil Kota berdasarkan ID Provinsi
    function getKota() {
        const idProv = document.getElementById('provinsi').value;
        resetSelect(['kota', 'kecamatan', 'kelurahan']);

        if (idProv == 0) return;

        axios.post("{{ route('get-kota') }}", {
            id: idProv,
            _token: "{{ csrf_token() }}"
        })
        .then(res => {
            if(res.data.status === 'success') {
                let options = '<option value="0">Pilih Kota</option>';
                res.data.data.forEach(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });
                document.getElementById('kota').innerHTML = options;
            }
        })
        .catch(err => console.error("Gagal mengambil kota:", err));
    }

    function getKecamatan() {
        const idKota = document.getElementById('kota').value;
        resetSelect(['kecamatan', 'kelurahan']);

        if (idKota == 0) return;

        axios.post("{{ route('get-kecamatan') }}", {
            id: idKota,
            _token: "{{ csrf_token() }}"
        })
        .then(res => {
            let options = '<option value="0">Pilih Kecamatan</option>';
            res.data.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            document.getElementById('kecamatan').innerHTML = options;
        });
    }

    function getKelurahan() {
        const idKec = document.getElementById('kecamatan').value;
        resetSelect(['kelurahan']);

        if (idKec == 0) return;

        axios.post("{{ route('get-kelurahan') }}", {
            id: idKec,
            _token: "{{ csrf_token() }}"
        })
        .then(res => {
            let options = '<option value="0">Pilih Kelurahan</option>';
            res.data.data.forEach(item => {
                options += `<option value="${item.id}">${item.name}</option>`;
            });
            document.getElementById('kelurahan').innerHTML = options;
        });
    }

    function updateText() {
        const prov = document.getElementById('provinsi').options[document.getElementById('provinsi').selectedIndex].text;
        const kota = document.getElementById('kota').options[document.getElementById('kota').selectedIndex].text;
        const kec = document.getElementById('kecamatan').options[document.getElementById('kecamatan').selectedIndex].text;
        const kel = document.getElementById('kelurahan').options[document.getElementById('kelurahan').selectedIndex].text;

        document.getElementById('resultText').innerText = `${kel}, ${kec}, ${kota}, ${prov}`;
    }
</script>
@endpush