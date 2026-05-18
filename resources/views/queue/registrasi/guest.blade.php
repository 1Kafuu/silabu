<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Nomor Antrian</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/queue/guest.css') }}">
</head>

</head>
<body>
    <div class="card-antrian">
        <div class="hospital-icon">
            <span class="mdi mdi-hospital-building"></span>
        </div>
        <h2>Ambil Nomor Antrian</h2>
        <p class="subtitle">Isi data di bawah untuk mendapatkan nomor antrian Anda.</p>

        @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin:0; padding-left:16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('queue-store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="customer_name">Nama Pasien</label>
                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    class="form-control"
                    placeholder="Masukkan nama lengkap"
                    value="{{ old('customer_name') }}"
                    required
                    autofocus
                >
            </div>
            <div class="form-group" style="margin-bottom: 28px;">
                <label for="poli_id">Pilih Poli</label>
                <select id="poli_id" name="poli_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Poli --</option>
                    @foreach ($polis as $poli)
                        <option value="{{ $poli->id }}" {{ old('poli_id') == $poli->id ? 'selected' : '' }}>
                            {{ $poli->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-submit">
                <span class="mdi mdi-ticket-confirmation"></span>
                Ambil Nomor Antrian
            </button>
        </form>
    </div>
</body>
</html>
