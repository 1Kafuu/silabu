@extends('layouts.app')

@section('title', 'Shipment Arrangement')
@section('page-title', 'Shipment')
@section('page-subtitle', 'Shipment Lists')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div id="notification-container"></div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title">Add Items</h4>
                                <p class="card-description">Add new items into the cart</p>
                            </div>
                            <a href="{{ route('shipment-datatables') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-table"></i>
                                <span class="mx-2">Datatables</span>
                            </a>
                        </div>
                        <form id="add-item-form">
                            <div class="form-group">
                                <label for="nama">Nama Barang</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required>
                            </div>
                            <div class="form-group">
                                <label for="harga">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" placeholder="Harga" required>
                            </div>
                            <button id="submit-button" type="submit" class="btn btn-success me-2">Add</button>
                        </form>
                        <div class="table-responsive" style="margin-top: 20px;">
                            <table class="table" border="1">
                                <thead>
                                    <tr>
                                        <th> No </th>
                                        <th> Name </th>
                                        <th> Price </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="empty-row">
                                        <td colspan="4" style="text-align: center">
                                            <p style="color: grey;">Cart is Empty</p>
                                        </td>
                                    </tr>
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
            let itemId = 0;

            $('#add-item-form').submit(function (event) {
                event.preventDefault();

                const nama = $('#nama').val().trim();
                const harga = $('#harga').val().trim();

                if (nama === '' || harga === '') {
                    showNotification('Please fill in all fields', 'danger');
                    return;
                }

                itemId++;

                const price = 'Rp' + parseInt(harga).toLocaleString('id-ID');
                const newRow = `  <tr>
                                    <td>${itemId}</td>
                                    <td>${nama}</td>
                                    <td>${price}</td>
                                  </tr>
                                `;

                $('#empty-row').remove();
                $('tbody').append(newRow);

                $('#nama').val('');
                $('#harga').val('');

                showNotification('Item added successfully', 'success');
            });

            function showNotification(message, type) {
                const notificationContainer = $('#notification-container');
                const notification = $(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <p>${message}</p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);

                notificationContainer.append(notification);

                setTimeout(function () {
                    notification.alert('close');
                }, 5000);
            }

            $('tbody').on('click', 'tr', function () {
                if ($(this).attr('id') === 'empty-row') return;

                currentRow = $(this);
                const id = currentRow.find('td:eq(0)').text();
                const nama = currentRow.find('td:eq(1)').text();
                const harga = currentRow.find('td:eq(2)').text().replace(/[^\d]/g, '');

                $('#edit-id').val(id);
                $('#edit-nama').val(nama);
                $('#edit-harga').val(harga);

                $('#editModal').modal('show');
            });

            // Handle Delete
            $('#btn-delete').click(function () {
                currentRow.remove();
                if ($('tbody tr').length === 0) {
                    $('tbody').append('<tr id="empty-row"><td colspan="3" style="text-align: center"><p style="color: grey;">Cart is Empty</p></td></tr>');
                }
                $('#editModal').modal('hide');
                showNotification('Item deleted successfully', 'warning');
            });

            // Handle Update
            $('#btn-update').click(function () {
                const newNama = $('#edit-nama').val();
                const newHarga = $('#edit-harga').val();

                if (newNama === '' || newHarga === '') {
                    alert('Nama dan Harga wajib diisi!');
                    return;
                }

                currentRow.find('td:eq(1)').text(newNama);
                currentRow.find('td:eq(2)').text('Rp' + parseInt(newHarga).toLocaleString('id-ID'));

                $('#editModal').modal('hide');
                showNotification('Item updated successfully', 'success');
            });
        });
    </script>
@endpush

@push('style-page')
    <style>
        tbody tr {
            cursor: pointer;
        }
    </style>
@endpush
