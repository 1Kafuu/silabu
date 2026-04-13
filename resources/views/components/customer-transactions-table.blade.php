@foreach ($pesanan as $row)
    <tr>
        <td>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $row->created_at->format('d M Y, H:i') }}</td>
        <td>{{ $row->metode_bayar }}</td>
        <td>
            @if ($row->status_bayar === 'pending')
                <span class="badge bg-warning text-dark">Pending</span>
            @elseif ($row->status_bayar === 'success')
                <span class="badge bg-success">Paid</span>
            @elseif ($row->status_bayar === 'failed')
                <span class="badge bg-danger">Failed</span>
            @else
                <span class="badge bg-secondary">{{ ucfirst($row->status_bayar) }}</span>
            @endif
        </td>
        <td class="font-weight-bold text-info">
            {{ Illuminate\Support\Number::currency($row->total, 'IDR', 'id') }}
        </td>
        <td>
            @if ($row->status_bayar === 'pending')
                <button type="button" class="btn btn-sm btn-primary"
                    onclick="bayarPesanan({{ $row->idpesanan }}, this)">Bayar</button>
            @else
                -
            @endif
        </td>
@endforeach