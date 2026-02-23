<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFGeneratorController extends Controller
{
    public function potrait()
    {
        $data = Buku::whereNull("deleted_at")->get();
        $pdf = Pdf::loadView('pdf.portrait', compact('data'));
        return $pdf->setPaper('a4', 'portrait')->download('data-buku.pdf');
    }

    public function landscape()
    {
        $data = Kategori::whereNull("deleted_at")->get();
        $pdf = Pdf::loadView('pdf.landscape', compact('data'));
        // return view('pdf.landscape', compact('data'));
        return $pdf->setPaper('a4', 'landscape')->download('data-category.pdf');
    }
}
