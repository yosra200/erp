<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class InvoicePrintController extends Controller
{
    public function sale(Invoice $invoice): View
    {
        $invoice->load(['customer', 'warehouse', 'user', 'items.product']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'sale',
            'isPdf' => false,
        ]);
    }

    public function salePdf(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'warehouse', 'user', 'items.product']);

        return $this->downloadPdf($invoice, 'sale');
    }

    public function purchase(PurchaseInvoice $invoice): View
    {
        $invoice->load(['supplier', 'warehouse', 'user', 'items.product']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'purchase',
            'isPdf' => false,
        ]);
    }

    public function purchasePdf(PurchaseInvoice $invoice): Response
    {
        $invoice->load(['supplier', 'warehouse', 'user', 'items.product']);

        return $this->downloadPdf($invoice, 'purchase');
    }

    private function downloadPdf(Invoice|PurchaseInvoice $invoice, string $invoiceType): Response
    {
        $html = view('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => $invoiceType,
            'isPdf' => true,
        ])->render();

        $pdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'default_font' => 'dejavusans',
            'directionality' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_top' => 14,
            'margin_right' => 14,
            'margin_bottom' => 14,
            'margin_left' => 14,
        ]);

        $pdf->SetDirectionality('rtl');
        $pdf->WriteHTML($html);

        $filename = $invoiceType === 'sale'
            ? $invoice->invoice_number.'.pdf'
            : $invoice->purchase_number.'.pdf';

        return response($pdf->Output('', Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
