<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function qrcode ($id){
        return QrCode::size(200)->generate($id);
    }
}
