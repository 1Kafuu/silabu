@extends('layouts.app')

@section('title', 'Shipment Arrangement')
@section('page-title', 'Shipment')
@section('page-subtitle', 'Shipment Lists')

@section('content')
    <div class="container">
        <!-- Form centered -->
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Add Items</h4>
                                <p class="card-description">Add new items into the cart</p>
                            </div>
                            <a href="{{ route('shipment') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-table"></i>
                                <span class="mx-2">HTML</span>
                            </a>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama Barang</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" placeholder="Harga" required>
                        </div>
                        <button id="submit-button" type="submit" class="btn btn-success me-2">Add</button>
                        <div class="table-responsive" style="margin-top: 20px;">
                            <table id="shipment-table" class="table" border="1">
                                <thead>
                                    <tr>
                                        <th> No </th>
                                        <th> Name </th>
                                        <th> Price </th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit/Delete Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="form-group mb-3">
                            <label>ID Barang</label>
                            <input type="text" class="form-control" id="edit-id" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" id="edit-nama" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Harga Barang</label>
                            <input type="number" class="form-control" id="edit-harga" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btn-delete">Hapus</button>
                    <button type="button" class="btn btn-primary" id="btn-update">Ubah</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js-page')
    <script>
        $(document).ready(function () {
            let table = $('#shipment-table').DataTable({
                columnDefs: [
                    { targets: 0, className: 'dt-body-left' },
                    { targets: 1, className: 'dt-body-left' },
                    { targets: 2, className: 'dt-body-right' }
                ],
                initComplete: function () {
                    $('#shipment-table thead th').eq(0).css('text-align', 'left');
                    $('#shipment-table thead th').eq(1).css('text-align', 'left');
                    $('#shipment-table thead th').eq(2).css('text-align', 'right');
                }
            });
            
            let itemId = 0;

            $('#submit-button').click(function () {
                addItems();
            });

            // Enter can submit the Input
            $('#nama, #harga').on('keypress', function (e) {
                if (e.which === 13) {
                    addItems();
                }
            });

            function addItems() {
                const nama = $('#nama').val().trim();
                const harga = $('#harga').val().trim();

                if (nama === '' || harga === '') {
                    showNotification('Please fill in all fields', 'danger');
                    return;
                }

                itemId++;

                const price = 'Rp' + parseInt(harga).toLocaleString('id-ID');
                table.row.add([
                    itemId,      // Kolom 1
                    nama,        // Kolom 2
                    price        // Kolom 3
                ]).draw(false);

                $('#nama').val('');
                $('#harga').val('');

                showNotification('Item added successfully', 'success');
            }

            function showNotification(message, type) {
                const notificationContainer = $('#notification-container');
                const notification = $(` <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                                    ${message}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>`);

                notificationContainer.append(notification);

                setTimeout(function () {
                    notification.find('.btn-close').trigger('click');
                }, 5000);
            }

            $('#shipment-table tbody').on('click', 'tr', function () {
                const data = table.row(this).data();
                if (!data) return;

                currentRowIndex = this;
                const id = data[0];
                const nama = data[1];
                const harga = data[2].replace(/[^\d]/g, '');

                $('#edit-id').val(id);
                $('#edit-nama').val(nama);
                $('#edit-harga').val(harga);
                $('#editModal').modal('show');
            });

            // Handle Delete
            $('#btn-delete').click(function () {
                table.row(currentRowIndex).remove().draw();
                $('#editModal').modal('hide');
                showNotification('Item deleted successfully', 'warning');
            });

            // Handle Update
            $('#btn-update').click(function () {
                const newNama = $('#edit-nama').val();
                const newHarga = $('#edit-harga').val();

                if (newNama === '' || newHarga === '') return;

                const formattedPrice = 'Rp' + parseInt(newHarga).toLocaleString('id-ID');

                // Update data di DataTables
                table.row(currentRowIndex).data([
                    $('#edit-id').val(),
                    newNama,
                    formattedPrice
                ]).draw();

                $('#editModal').modal('hide');
                showNotification('Item updated successfully', 'success');
            });
        });
    </script>
@endpush

@push('style-page')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css" />
    <style>
        #shipment-table tbody tr {
            cursor: pointer;
        }
    </style>
@endpush

@push('js-page')
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>
@endpush