<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoicePrintController extends Controller
{
    public function sale(Invoice $invoice): View
    {
        $invoice->load(['customer', 'warehouse', 'user', 'items.product']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'sale',
        ]);
    }

    public function salePdf(Invoice $invoice): Response
    {
        $invoice->load(['customer', 'warehouse', 'user', 'items.product']);

        return Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'sale',
        ])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->download($invoice->invoice_number.'.pdf');
    }

    public function purchase(PurchaseInvoice $invoice): View
    {
        $invoice->load(['supplier', 'warehouse', 'user', 'items.product']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'purchase',
        ]);
    }

    public function purchasePdf(PurchaseInvoice $invoice): Response
    {
        $invoice->load(['supplier', 'warehouse', 'user', 'items.product']);

        return Pdf::loadView('invoices.print', [
            'invoice' => $invoice,
            'invoiceType' => 'purchase',
        ])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->download($invoice->purchase_number.'.pdf');
    }
}
