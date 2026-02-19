<?php

namespace App\Http\Controllers\Peasant;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::where('peasant_id', Auth::id())
            ->with('coffeeType')
            ->orderBy('purchase_date', 'desc')
            ->paginate(15);

        return view('peasant.purchases.index', compact('purchases'));
    }

    public function show(Purchase $purchase)
    {
        // Asegurar que la compra pertenece al usuario autenticado
        if ($purchase->peasant_id !== Auth::id()) {
            abort(403);
        }

        $purchase->load(['coffeeType', 'payments', 'invoice']);

        return view('peasant.purchases.show', compact('purchase'));
    }
}

