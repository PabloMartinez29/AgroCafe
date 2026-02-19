<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Administradores (y cualquier usuario no campesino) ven todas las facturas del sistema
        if ($user->isPeasant()) {
            $query = Invoice::where('transaction_type', 'purchase')
                ->whereHas('purchase', function ($q) use ($user) {
                    $q->where('peasant_id', $user->id);
                })
                ->with(['purchase.coffeeType', 'purchase.peasant']);
        } else {
            $query = Invoice::with([
                'sale.coffeeType',
                'sale.cooperative',
                'purchase.coffeeType',
                'purchase.peasant',
            ]);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15);

        return response()->json($invoices);
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        // Los campesinos solo pueden ver sus propias facturas
        if ($user->isPeasant()) {
            if ($invoice->transaction_type !== 'purchase' || 
                $invoice->purchase->peasant_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $invoice->load([
            'sale.coffeeType', 
            'sale.cooperative', 
            'purchase.coffeeType', 
            'purchase.peasant'
        ]);

        return response()->json($invoice);
    }
}

