<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PeasantPayment;
use App\Models\Sale;
use App\Models\Purchase;
use App\Services\InvoiceGeneratorService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'sales'); // 'sales' = ventas o 'peasants' = campesinos

        if ($type === 'peasants') {
            $payments = PeasantPayment::with(['purchase.peasant', 'purchase.coffeeType'])
                ->orderBy('payment_date', 'desc')
                ->get();
        } else {
            $payments = Payment::with(['sale.cooperative', 'sale.coffeeType'])
                ->orderBy('payment_date', 'desc')
                ->get();
        }

        return view('admin.payments.index', compact('payments', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'sales');
        return view('admin.payments.create', compact('type'));
    }

    public function store(Request $request)
    {
        $type = $request->get('type', 'sales');

        if ($type === 'peasants') {
            $validated = $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:transfer,cash,check',
                'reference' => 'nullable|string|max:100',
                'payment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
            ]);

            $payment = PeasantPayment::create($validated);
            
            // Si el pago está completado, actualizar estado de la compra y generar factura
            if ($validated['status'] === 'completed') {
                $purchase = Purchase::find($validated['purchase_id']);
                if ($purchase) {
                    $purchase->update(['status' => 'completed']);
                    
                    // Generar factura automáticamente si no existe
                    InvoiceGeneratorService::forPurchase($purchase, $validated['payment_date']);
                }
            }
        } else {
            $validated = $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:transfer,cash,check',
                'reference' => 'nullable|string|max:100',
                'payment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
            ]);

            $payment = Payment::create($validated);
            
            // Si el pago está completado, actualizar estado de la venta y generar factura
            if ($validated['status'] === 'completed') {
                $sale = Sale::find($validated['sale_id']);
                if ($sale) {
                    $sale->update(['status' => 'completed']);
                    
                    // Generar factura automáticamente si no existe
                    InvoiceGeneratorService::forSale($sale, $validated['payment_date']);
                }
            }
        }

        return redirect()->route('admin.payments.index', ['type' => $type])
            ->with('success', 'Pago registrado exitosamente y factura generada automáticamente.');
    }
}

