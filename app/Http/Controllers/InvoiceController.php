<?php

namespace App\Http\Controllers;

use App\Models\GuestFolio;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(GuestFolio $folio)
    {
        $folio->load(['guest', 'checkIn.reservation.room.roomType', 'items']);

        $pdf = Pdf::loadView('pdf.invoice', compact('folio'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $folio->folio_number . '.pdf');
    }
}
