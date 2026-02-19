<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = [
                'total_purchases' => Purchase::where('status', 'completed')->count(),
                'total_sales' => Sale::where('status', 'completed')->count(),
                'pending_purchases' => Purchase::where('status', 'pending')->count(),
                'pending_sales' => Sale::where('status', 'pending')->count(),
            ];
        } else {
            $stats = [
                'total_purchases' => Purchase::where('peasant_id', $user->id)->count(),
                'completed_purchases' => Purchase::where('peasant_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'pending_purchases' => Purchase::where('peasant_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
            ];
        }

        return response()->json($stats);
    }
}

