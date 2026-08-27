<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoiceType === 'sale' ? 'فاتورة بيع' : 'فاتورة شراء' }} - {{ $invoiceType === 'sale' ? $invoice->invoice_number : $invoice->purchase_number }}</title>
    <style>
        @page { margin: 22px 24px; }
        * { box-sizing: border-box; }
        body { direction: rtl; font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 0; }
        .header { border-bottom: 3px solid #0f766e; padding-bottom: 16px; margin-bottom: 18px; }
        .brand { font-size: 24px; font-weight: bold; color: #0f766e; }
        .subtitle { color: #64748b; margin-top: 5px; }
        .invoice-title { text-align: left; font-size: 22px; font-weight: bold; color: #0f172a; }
        .header-table, .info-table, .totals-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .info-table { margin-bottom: 18px; border: 1px solid #dbe4ea; }
        .info-table td { padding: 9px 11px; border-left: 1px solid #dbe4ea; }
        .info-table td:last-child { border-left: 0; }
        .label { color: #64748b; font-size: 10px; display: block; margin-bottom: 3px; }
        .value { font-weight: bold; }
        .items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .items th { background: #0f766e; color: #fff; padding: 9px 7px; font-size: 11px; }
        .items td { border-bottom: 1px solid #e2e8f0; padding: 8px 7px; }
        .items tr:nth-child(even) td { background: #f8fafc; }
        .number { direction: ltr; text-align: left; white-space: nowrap; }
        .barcode { font-family: monospace; direction: ltr; font-size: 10px; color: #475569; }
        .bottom { margin-top: 20px; width: 100%; }
        .notes { color: #64748b; width: 55%; vertical-align: top; padding-top: 8px; }
        .totals { width: 45%; vertical-align: top; }
        .totals-table td { padding: 7px 5px; border-bottom: 1px solid #e2e8f0; }
        .totals-table td:last-child { text-align: left; direction: ltr; }
        .grand-total td { font-size: 16px; font-weight: bold; color: #0f766e; border-bottom: 3px solid #0f766e; }
        .footer { margin-top: 35px; border-top: 1px solid #cbd5e1; padding-top: 10px; color: #64748b; text-align: center; font-size: 10px; }
        .print-button { position: fixed; left: 20px; top: 20px; background: #0f766e; color: #fff; border: 0; padding: 9px 14px; border-radius: 5px; cursor: pointer; }
        .pdf-only { display: none; }
        @media print { .print-button { display: none; } }
    </style>
</head>
<body>
    @unless($isPdf ?? false)
        <button class="print-button" onclick="window.print()">طباعة الفاتورة</button>
    @endunless

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <div class="brand">نظام ERP</div>
                    <div class="subtitle">إدارة المبيعات والمشتريات والمخزون</div>
                </td>
                <td class="invoice-title">{{ $invoiceType === 'sale' ? 'فاتورة بيع' : 'فاتورة شراء' }}</td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <span class="label">رقم الفاتورة</span>
                <span class="value">{{ $invoiceType === 'sale' ? $invoice->invoice_number : $invoice->purchase_number }}</span>
            </td>
            <td>
                <span class="label">التاريخ</span>
                <span class="value">{{ ($invoiceType === 'sale' ? $invoice->invoice_date : $invoice->purchase_date)?->format('Y-m-d') }}</span>
            </td>
            <td>
                <span class="label">{{ $invoiceType === 'sale' ? 'العميل' : 'المورد' }}</span>
                <span class="value">{{ $invoiceType === 'sale' ? ($invoice->customer?->name ?? 'عميل نقدي') : ($invoice->supplier?->name ?? 'بدون مورد') }}</span>
            </td>
            <td>
                <span class="label">المخزن</span>
                <span class="value">{{ $invoice->warehouse?->name ?? '-' }}</span>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th style="text-align:right">المنتج</th>
                <th>الباركود</th>
                <th>الكمية</th>
                <th>{{ $invoiceType === 'sale' ? 'سعر البيع' : 'تكلفة الشراء' }}</th>
                <th>الخصم</th>
                <th>الضريبة</th>
                <th>الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product?->name ?? '-' }}</td>
                    <td class="barcode">{{ $item->product?->barcode ?? '-' }}</td>
                    <td class="number">{{ number_format((float) $item->quantity, 3) }}</td>
                    <td class="number">{{ number_format((float) ($invoiceType === 'sale' ? $item->unit_price : $item->cost_price), 2) }}</td>
                    <td class="number">{{ number_format((float) $item->discount, 2) }}</td>
                    <td class="number">{{ number_format((float) $item->tax, 2) }}</td>
                    <td class="number">{{ number_format((float) $item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="bottom">
        <tr>
            <td class="notes">
                <strong>ملاحظات</strong><br>
                نشكركم لتعاملكم معنا.<br>
                تم إصدار هذه الفاتورة بواسطة {{ $invoice->user?->name ?? 'مستخدم النظام' }}.
            </td>
            <td class="totals">
                <table class="totals-table">
                    <tr><td>الإجمالي قبل الخصم</td><td>{{ number_format((float) $invoice->subtotal, 2) }} ج.م</td></tr>
                    <tr><td>الخصم</td><td>{{ number_format((float) $invoice->discount, 2) }} ج.م</td></tr>
                    <tr><td>الضريبة</td><td>{{ number_format((float) $invoice->tax, 2) }} ج.م</td></tr>
                    <tr class="grand-total"><td>الإجمالي النهائي</td><td>{{ number_format((float) $invoice->total, 2) }} ج.م</td></tr>
                    <tr><td>المدفوع</td><td>{{ number_format((float) $invoice->paid, 2) }} ج.م</td></tr>
                    <tr><td>المتبقي</td><td>{{ number_format(max(0, (float) $invoice->total - (float) $invoice->paid), 2) }} ج.م</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">هذه الفاتورة صادرة إلكترونيًا من نظام ERP</div>
</body>
</html>
