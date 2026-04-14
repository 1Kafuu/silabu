<style>
    @page {
        size: 21cm 17cm;
        margin: 0.3cm;
    }

    body {
        margin: 0;
        padding: 0;
        font-size: 10px;
    }

    .sheet {
        border-collapse: collapse;
        width: 20.4cm;
        height: 16.4cm;
        margin: 0 auto;
        table-layout: fixed;
    }

    .sheet td.label-cell {
        padding: 0;
        width: 4.1cm;
        height: 2.0cm;
    }

    .label {
        width: 3.8cm;
        height: 1.8cm;
        overflow: hidden;
        border: 1px solid white;
        box-sizing: border-box;
        background: #fff;
        text-align: center;
    }

    .text {
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        line-height: 1.1;
        margin: 12px;
        /* border: red 1px solid; */
    }

    .nama {
        font-size: 8px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .harga {
        font-size: 14px;
        font-weight: bold;
        margin: 2px 0;
    }

    .kode {
        margin-top: 4px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .kode img {
        width: auto;
        max-width: 100px;
        height: 20px;
        display: block;
    }

    .id-text {
        font-size: 7px;
        font-family: 'Courier New', Courier, monospace;
        margin-top: 1px;
        letter-spacing: 1px;
        color: #333;
    }
</style>

<table class="sheet">
    @php $index = 0; @endphp
    @for ($row = 0; $row < 8; $row++)
        <tr>
            @for ($col = 0; $col < 5; $col++)
                @php
                    $key = ($row + 1) . '-' . ($col + 1);
                @endphp
                <td class="label-cell">
                    <div class="label">
                        @if(in_array($key, $selected ?? []) && $index < count($dataToPrint ?? []))
                            <div class="text">
                                <div class="nama">{{ $dataToPrint[$index]->nama ?? '' }}</div>
                                <div class="harga">
                                    {{ Illuminate\Support\Number::currency($dataToPrint[$index]->harga ?? 0, 'IDR', 'id') }}
                                </div>
                                <div class="kode">
                                    {{-- Barcode di bagian atas --}}
                                    <img src="data:image/png;base64,{{ $dataToPrint[$index]->barcode_base64 ?? '' }}" alt="Barcode">

                                    {{-- ID Barang di bagian bawah --}}
                                    <div class="id-text">
                                        {{ $dataToPrint[$index]->id_barang ?? '' }}
                                    </div>
                                </div>
                            </div>
                            @php $index++; @endphp
                        @else
                            <div class="text">
                                <div class="nama"></div>
                                <div class="harga"></div>
                                <div class="kode"></div>
                            </div>
                        @endif
                    </div>
                </td>
            @endfor
        </tr>
    @endfor
</table>