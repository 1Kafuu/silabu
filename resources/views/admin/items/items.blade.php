@extends('layouts.app')

@section('title', 'Items Managements')
@section('page-title', 'Items')
@section('page-subtitle', 'Items Lists')

@section('content')
    <div id="notification-container"></div>
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Items</h4>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                <span class="mx-2">PDF</span>
                                <i class="mdi mdi-file-export"></i>
                            </button>
                            <a href="{{ route('create-items') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-bookmark-plus"></i>
                                <span class="mx-2">Add Items</span>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="text-align:center">
                                        <input type="checkbox" name="select_all" id="select-all">
                                    </th>
                                    <th> No </th>
                                    <th> UID </th>
                                    <th> Name </th>
                                    <th> Price </th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @foreach ($barang as $row)
                                        <tr>
                                            <td style="text-align: center">
                                                <input type="checkbox" name="selected_items[]" value="{{ $row->id_barang }}">
                                            </td>
                                            <td>
                                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </td>
                                            <td>
                                                {{ $row->id_barang }}
                                            </td>
                                            <td>
                                                {{ $row->nama }}
                                            </td>
                                            <td>
                                                {{ Illuminate\Support\Number::currency($row->harga, 'IDR', 'id') }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-end gap-2">
                                                    <a href={{ route('edit-items', ['id' => $row->id_barang]) }}
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="mdi mdi-account-edit"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('delete-items', ['id' => $row->id_barang]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                                            <i class="mdi mdi-account-remove"></i>
                                                            <span>Delete</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('partials._label')
        </div>
@endsection

    @push('style-page')
        <style>
            .sheet-preview {
                display: grid;
                grid-template-columns: repeat(5, 60px);
                gap: 5px;
                align-items: center;
                justify-content: center;
            }

            .slot {
                height: 40px;
                border: 1px solid black;
                cursor: pointer;
                cursor: pointer;
                transition: all 0.2s;
            }

            .slot:hover {
                background-color: lightgreen;
            }

            .slot.active {
                background-color: green;
                border-color: lightgreen;
            }
        </style>
    @endpush

    @push('js-page')
        <script>
            let selectedSlots = [];
            let selectedItems = [];

            // Fungsi untuk update hidden input
            function updateSelectedItems() {
                document.getElementById('selected_items').value = JSON.stringify(selectedItems);
            }

            // Handle select all checkbox
            document.getElementById('select-all').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
                const isChecked = this.checked;

                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                    const value = checkbox.value;

                    if (isChecked) {
                        if (!selectedItems.includes(value)) {
                            selectedItems.push(value);
                        }
                    } else {
                        selectedItems = [];
                    }
                });

                updateSelectedItems();
            });

            // Handle individual checkboxes
            document.querySelectorAll('input[name="selected_items[]"]').forEach(el => {
                el.addEventListener('change', function () {
                    let value = this.value;

                    if (this.checked) {
                        if (!selectedItems.includes(value)) {
                            selectedItems.push(value);
                        }
                    } else {
                        selectedItems = selectedItems.filter(id => id !== value);

                        // Uncheck select all if any checkbox is unchecked
                        document.getElementById('select-all').checked = false;
                    }

                    // Check if all checkboxes are checked
                    const allCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');
                    const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                    document.getElementById('select-all').checked = allChecked;

                    updateSelectedItems();
                });
            });

            // Handle slot clicks
            document.querySelectorAll('.slot').forEach(el => {
                el.addEventListener('click', function () {
                    let row = this.dataset.row;
                    let col = this.dataset.col;
                    let key = row + '-'.col;

                    const selectedCount = selectedItems.length;

                    if (selectedCount === 0) {
                        alert('Pilih item terlebih dahulu!');
                        return;
                    }

                    let startIndex = (parseInt(row) - 1) * 5 + (parseInt(col) - 1);
                    let allSlots = Array.from(document.querySelectorAll('.slot'));

                    document.querySelectorAll('.slot').forEach(slot => {
                        slot.classList.remove('active');
                    });
                    selectedSlots = [];


                    for (let i = startIndex; i < allSlots.length && i < startIndex + selectedCount; i++) {
                        let slot = allSlots[i];
                        let slotRow = slot.dataset.row;
                        let slotCol = slot.dataset.col;
                        let slotKey = slotRow + '-' + slotCol;

                        slot.classList.add('active');
                        selectedSlots.push(slotKey);
                    }

                    document.getElementById('selected_slots').value = JSON.stringify(selectedSlots);
                });
            });
        </script>
    @endpush

    @push('js-page')
        <script>
            $(document).ready(function () {
                console.log('Document ready');
                let notification = sessionStorage.getItem('notification');
                console.log(notification);
                if (notification) {
                    $('#notification-container').html(notification);
                    sessionStorage.removeItem('notification');

                    // Auto dismiss after 5 seconds
                    setTimeout(function () {
                        $('.alert').fadeOut('slow', function () {
                            $(this).remove();
                        });
                    }, 5000);
                }
            });
        </script>
    @endpush