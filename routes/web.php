<?php

use App\Http\Controllers\InvoicePrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::middleware('auth')->group(function (): void {
    Route::get('/invoices/{invoice}/print', [InvoicePrintController::class, 'sale'])->name('invoices.print');
    Route::get('/invoices/{invoice}/pdf', [InvoicePrintController::class, 'salePdf'])->name('invoices.pdf');
    Route::get('/purchase-invoices/{invoice}/print', [InvoicePrintController::class, 'purchase'])->name('purchase-invoices.print');
    Route::get('/purchase-invoices/{invoice}/pdf', [InvoicePrintController::class, 'purchasePdf'])->name('purchase-invoices.pdf');
});
