<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Choose Print Position</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('label') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="selected_slots" id="selected_slots">
                    <input type="hidden" name="selected_items" id="selected_items">

                    <div class="sheet-preview">
                        @for ($row = 1; $row <= 8; $row++)
                            @for ($col = 1; $col <= 5; $col++)
                                <div class="slot" data-row="{{ $row }}" data-col="{{ $col }}">
                                </div>
                            @endfor
                        @endfor
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="Submit" class="btn btn-primary">Print</button>
                </div>
            </form>
        </div>
    </div>
</div>