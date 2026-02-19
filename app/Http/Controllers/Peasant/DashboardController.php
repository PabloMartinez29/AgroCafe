<?php

namespace App\Http\Controllers\Peasant;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_purchases' => Purchase::where('peasant_id', $user->id)->count(),
            'completed_purchases' => Purchase::where('peasant_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'pending_purchases' => Purchase::where('peasant_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        $recent_purchases = Purchase::where('peasant_id', $user->id)
            ->with('coffeeType')
            ->orderBy('purchase_date', 'desc')
            ->limit(5)
            ->get();

        return view('peasant.dashboard', compact('stats', 'recent_purchases'));
    }
}

