<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFGeneratorController extends Controller
{
    public function potrait()
    {
        $data = Buku::whereNull("deleted_at")->get();
        $pdf = Pdf::loadView('pdf.portrait', compact('data'));
        return $pdf->setPaper('a4', 'portrait')->stream('data-buku.pdf');
    }

    public function landscape()
    {
        $data = Kategori::whereNull("deleted_at")->get();
        $pdf = Pdf::loadView('pdf.landscape', compact('data'));
        // return view('pdf.landscape', compact('data'));
        return $pdf->setPaper('a4', 'landscape')->stream('data-category.pdf');
    }

    public function label(Request $request)
    {
        $selected = json_decode($request->selected_slots, true);
        $items = json_decode($request->selected_items, true);

        $dataBarang = Barang::whereIn('id_barang', $items)->get();

        $pdf = Pdf::loadView('pdf.label', [
            'selected' => $selected,
            'dataToPrint' => $dataBarang
        ])->setPaper([0, 0, 595, 508]);

        return $pdf->stream();
    }
}
