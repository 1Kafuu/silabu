@extends('layouts.app')

@section('title', 'Student Management')
@section('page-title', 'Students')
@section('page-subtitle', 'Student List')

@section('content')
    <div id="notification-container"></div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Students</h4>
                        <a href="{{ route('create-student') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-account-plus"></i>
                            <span class="d-none d-sm-inline mx-1">Add Student</span>
                        </a>
                    </div>

                    {{-- Desktop table --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Name</th>
                                    <th width="20%">Email</th>
                                    <th width="12%">NIM</th>
                                    <th width="15%">Fakultas</th>
                                    <th width="15%">Prodi</th>
                                    <th width="13%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students as $row)
                                    <tr>
                                        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $row->user->name ?? '-' }}</td>
                                        <td>{{ $row->user->email ?? '-' }}</td>
                                        <td>{{ $row->NIM }}</td>
                                        <td>{{ $row->fakultas }}</td>
                                        <td>{{ $row->prodi }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('edit-student', ['id' => $row->id]) }}"
                                                    class="btn btn-outline-success btn-sm">
                                                    <i class="mdi mdi-pencil"></i>
                                                    <span>Edit</span>
                                                </a>
                                                <form method="POST"
                                                    action="{{ route('delete-student', ['id' => $row->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus student ini?')">
                                                        <i class="mdi mdi-delete"></i>
                                                        <span>Delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards --}}
                    <div class="d-block d-md-none">
                        @forelse ($students as $row)
                            <div class="student-card mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $row->user->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $row->user->email ?? '-' }}</small>
                                    </div>
                                    <span class="badge bg-secondary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block"><strong>NIM:</strong> {{ $row->NIM }}</small>
                                    <small class="text-muted d-block"><strong>Fakultas:</strong> {{ $row->fakultas }}</small>
                                    <small class="text-muted d-block"><strong>Prodi:</strong> {{ $row->prodi }}</small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('edit-student', ['id' => $row->id]) }}"
                                        class="btn btn-outline-success btn-sm flex-fill text-center">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('delete-student', ['id' => $row->id]) }}"
                                        class="flex-fill">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus student ini?')">
                                            <i class="mdi mdi-delete"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">No students found.</div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script>
        $(document).ready(function() {
            let notification = sessionStorage.getItem('notification');
            if (notification) {
                $('#notification-container').html(notification);
                sessionStorage.removeItem('notification');

                setTimeout(function() {
                    $('.alert').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endpush

@push('style-page')
    <style>
        .student-card {
            background-color: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        }

        .student-card:last-child {
            margin-bottom: 0 !important;
        }
    </style>
@endpush
