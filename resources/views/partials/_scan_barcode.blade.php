<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanModalLabel">Scan Label</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="reader" style="width: 100% text-align-center;"></div>
                <div id="scan-result" class="mt-3 d-none">
                    <p>Hasil Scan:</p>
                    <div class="alert alert-success text-start">
                        <strong>ID Barang:</strong> <span id="result-id"></span><br>
                        <strong>Nama:</strong> <span id="result-nama"></span><br>
                        <strong>Harga:</strong> <span id="result-harga"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>