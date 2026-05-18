@extends('layouts.app')

@section('title', 'Poli Management')
@section('page-title', 'Poli')
@section('page-subtitle', 'Daftar Poli')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Daftar Poli</h4>
                        <a href="{{ route('create-poli') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-plus"></i>
                            <span class="mx-2">Tambah Poli</span>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="8%">No</th>
                                    <th width="35%">Nama Poli</th>
                                    <th width="15%">Kode</th>
                                    <th width="15%">Status</th>
                                    <th width="27%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($polis as $row)
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            <span class="badge badge-outline-primary">{{ $row->code }}</span>
                                        </td>
                                        <td>
                                            @if ($row->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('edit-poli', ['id' => $row->id]) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST" action="{{ route('delete-poli', ['id' => $row->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus poli ini?')">
                                                        <i class="mdi mdi-delete"></i>
                                                        <span>Hapus</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="mdi mdi-hospital-building" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                            Belum ada data poli.
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
    <script>
        $(document).ready(function () {
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');

                setTimeout(function () {
                    $('.alert').fadeOut('slow', function () {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endpush
