<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PeasantPayment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\InvoiceGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $type = $request->get('type', 'sales');
        $query = $type === 'peasants' 
            ? PeasantPayment::with(['purchase.peasant', 'purchase.coffeeType'])
            : Payment::with(['sale.cooperative', 'sale.coffeeType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return response()->json($payments);
    }

    public function show($id, Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $type = $request->get('type', 'sales');
        
        if ($type === 'peasants') {
            $payment = PeasantPayment::with(['purchase.peasant', 'purchase.coffeeType'])->findOrFail($id);
        } else {
            $payment = Payment::with(['sale.cooperative', 'sale.coffeeType'])->findOrFail($id);
        }

        return response()->json($payment);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

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
            $payment->load(['purchase.peasant', 'purchase.coffeeType']);

            if ($validated['status'] === 'completed') {
                $purchase = Purchase::find($validated['purchase_id']);
                if ($purchase) {
                    $purchase->update(['status' => 'completed']);
                    InvoiceGeneratorService::forPurchase($purchase, $validated['payment_date']);
                }
            }

            return response()->json($payment, 201);
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
            $payment->load(['sale.cooperative', 'sale.coffeeType']);

            if ($validated['status'] === 'completed') {
                $sale = Sale::find($validated['sale_id']);
                if ($sale) {
                    $sale->update(['status' => 'completed']);
                    InvoiceGeneratorService::forSale($sale, $validated['payment_date']);
                }
            }

            return response()->json($payment, 201);
        }
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $type = $request->get('type', 'sales');

        if ($type === 'peasants') {
            $payment = PeasantPayment::findOrFail($id);
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:transfer,cash,check',
                'reference' => 'nullable|string|max:100',
                'payment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
            ]);
        } else {
            $payment = Payment::findOrFail($id);
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:transfer,cash,check',
                'reference' => 'nullable|string|max:100',
                'payment_date' => 'required|date',
                'status' => 'required|in:pending,completed,failed',
            ]);
        }

        $payment->update($validated);

        if ($validated['status'] === 'completed') {
            if ($type === 'peasants') {
                $purchase = $payment->purchase;
                if ($purchase) {
                    $purchase->update(['status' => 'completed']);
                    InvoiceGeneratorService::forPurchase($purchase, $validated['payment_date']);
                }
            } else {
                $sale = $payment->sale;
                if ($sale) {
                    $sale->update(['status' => 'completed']);
                    InvoiceGeneratorService::forSale($sale, $validated['payment_date']);
                }
            }
        }

        $payment->load($type === 'peasants'
            ? ['purchase.peasant', 'purchase.coffeeType']
            : ['sale.cooperative', 'sale.coffeeType']);

        return response()->json($payment);
    }

    public function destroy($id, Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $type = $request->get('type', 'sales');
        $payment = $type === 'peasants' 
            ? PeasantPayment::findOrFail($id)
            : Payment::findOrFail($id);

        $payment->delete();

        return response()->json(['message' => 'Pago eliminado exitosamente']);
    }
}

