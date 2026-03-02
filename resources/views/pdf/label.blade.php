<style>
    @page {
        margin: 0.3cm;
    }

    body {
        margin: 0;
        padding: 0;
    }

    .sheet {
        border-collapse: collapse;
        width: 20cm;
    }

    .sheet td {
        padding: 0;
        vertical-align: top;
    }

    .label {
        width: 3.8cm;
        height: 1.8cm;
        font-size: 9px;
        overflow: hidden;
        border: 1px solid black;
    }

    .text {
        padding: 5px;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
    }

    .text .nama {
        font-size: 9px;
        font-weight: medium;
    }

    .text .harga {
        font-size: 15px;
        font-weight: bold;
    }

    .text .kode {
        margin-top: 3px;
        font-size: 10px;
    }

    .gap-x {
        width: 0.3cm;
    }

    .gap-y {
        height: 0.3cm;
    }
</style>
<table class="sheet"> @php $index = 0; @endphp

    @for ($row = 1; $row <= 8; $row++)
        <tr>
            @for ($col = 1; $col <= 5; $col++)
                @php
                    $key = $row . '-' . $col;
                @endphp

                <td>
                    <div class="label">
                        @if(in_array($key, $selected) && $index < count($dataToPrint))
                            <div class="text">
                                <div class="nama">
                                    {{ $dataToPrint[$index]->nama }}
                                </div>
                                <div class="harga">
                                    {{ Illuminate\Support\Number::currency($dataToPrint[$index]->harga, 'IDR', 'id') }}
                                </div>
                                <div class="kode">
                                    ID: {{ $dataToPrint[$index]->id_barang }}
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
                <td class="gap-x"></td>
            @endfor
        </tr>
        <tr>
            <td colspan="9" class="gap-y"></td>
        </tr>
    @endfor

</table>