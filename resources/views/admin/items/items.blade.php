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
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#labelModal">
                                <span class="d-none d-sm-inline mx-1">PDF</span>
                                <i class="mdi mdi-file-export"></i>
                            </button>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#scanModal">
                                <i class="mdi mdi-line-scan"></i>
                                <span class="d-none d-sm-inline mx-1">Scan Label</span>
                            </button>
                            <a href="{{ route('create-items') }}" class="btn btn-success btn-sm">
                                <i class="mdi mdi-bookmark-plus"></i>
                                <span class="d-none d-sm-inline mx-1">Add Items</span>
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
                                @foreach ($barang as $row)
                                    <tr>
                                        <td style="text-align: center">
                                            <input type="checkbox" name="selected_items[]" value="{{ $row->id_barang }}">
                                        </td>
                                        <td>
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            <img src="data:image/png;base64,{{ $row->barcode_base64 }}" alt="Barcode">
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @include('partials._label')
            @include('partials._scan_barcode')
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
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            let html5QrcodeScanner = null;
            let lastScannedCode = null;
            const beepSound = new Audio('{{ asset('music/scanner-beep.mp3') }}');

            document.getElementById('scanModal').addEventListener('show.bs.modal', function () {
                lastScannedCode = null;
                document.getElementById('scan-result').classList.add('d-none');
                document.getElementById('result-id').textContent = '';
                document.getElementById('result-nama').textContent = '';
                document.getElementById('result-harga').textContent = '';

                html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                    fps: 10,
                    qrbox: { width: 300, height: 100 },
                    rememberLastUsedCamera: true,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E,
                        Html5QrcodeSupportedFormats.CODABAR,
                        Html5QrcodeSupportedFormats.ITF,
                    ]
                }, false);

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);

                const observer = new MutationObserver(() => {
                    const btnStart = document.getElementById('html5-qrcode-button-camera-start');
                    const btnStop = document.getElementById('html5-qrcode-button-camera-stop');
                    const selectCamera = document.getElementById('html5-qrcode-select-camera');
                    
                    
                    if (btnStart && !btnStart.classList.contains('btn')) {
                        btnStart.className = 'btn btn-gradient-primary mt-2 mx-2';
                        btnStart.style.cssText = '';
                        btnStart.style.display = 'inline-block';
                    }
                    
                    if (btnStop && !btnStop.classList.contains('btn')) {
                        btnStop.className = 'btn btn-danger mt-2';
                        btnStop.style.cssText = '';
                        btnStop.style.display = 'inline-block';
                    }

                    if (selectCamera && !selectCamera.classList.contains('form-select')) {
                        selectCamera.className = 'form-select form-select-sm mt-2 mb-2 d-inline-block';
                        selectCamera.style.cssText = 'width: 90%; color: #333;';
                    }
                });

                observer.observe(document.getElementById('reader'), { childList: true, subtree: true });
            });

            document.getElementById('scanModal').addEventListener('hide.bs.modal', function () {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        html5QrcodeScanner = null;
                    }).catch(error => {
                        console.error("Failed to clear html5QrcodeScanner", error);
                    });
                }
            });

            function onScanSuccess(decodedText, decodedResult) {
                if (decodedText === lastScannedCode) return;
                lastScannedCode = decodedText;

                beepSound.play();

                fetch(`{{ url('items/barcode') }}/${decodedText}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('result-id').textContent = data.data.id_barang;
                            document.getElementById('result-nama').textContent = data.data.nama;
                            document.getElementById('result-harga').textContent = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.data.harga);
                            document.getElementById('scan-result').classList.remove('d-none');
                        } else {
                            alert(data.message || 'Barang tidak ditemukan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mencari barang');
                    });
            }

            function onScanFailure(error) {
            }
        </script>
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
                    let key = row + '-' + col;

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

                    setTimeout(function () {
                        $('.alert').fadeOut('slow', function () {
                            $(this).remove();
                        });
                    }, 5000);
                }
            });
        </script>
    @endpush
