<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;

class InvoiceGeneratorService
{
    /**
     * Generar factura automáticamente para una venta (si no existe).
     */
    public static function forSale(Sale $sale, $paymentDate): Invoice
    {
        $existingInvoice = Invoice::where('sale_id', $sale->id)
            ->where('transaction_type', 'sale')
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        $invoiceNumber = 'F' . str_pad($sale->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
        $total = $sale->quantity * $sale->price_per_kg;

        return Invoice::create([
            'sale_id' => $sale->id,
            'purchase_id' => null,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $paymentDate,
            'subtotal' => $total,
            'taxes' => 0,
            'total' => $total,
            'payment_status' => 'paid',
            'due_date' => Carbon::parse($paymentDate)->addDays(30),
            'transaction_type' => 'sale',
        ]);
    }

    /**
     * Generar factura automáticamente para una compra (si no existe).
     */
    public static function forPurchase(Purchase $purchase, $paymentDate): Invoice
    {
        $existingInvoice = Invoice::where('purchase_id', $purchase->id)
            ->where('transaction_type', 'purchase')
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        $invoiceNumber = 'FC' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y');
        $total = $purchase->quantity * $purchase->price_per_kg;

        return Invoice::create([
            'sale_id' => null,
            'purchase_id' => $purchase->id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $paymentDate,
            'subtotal' => $total,
            'taxes' => 0,
            'total' => $total,
            'payment_status' => 'paid',
            'due_date' => Carbon::parse($paymentDate)->addDays(30),
            'transaction_type' => 'purchase',
        ]);
    }
}
