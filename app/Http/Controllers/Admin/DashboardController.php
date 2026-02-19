<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Models\CoffeeType;
use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'total_purchases' => Purchase::where('status', 'completed')->count(),
            'total_sales' => Sale::where('status', 'completed')->count(),
            'total_coffee_types' => CoffeeType::active()->count(),
            'total_cooperatives' => Cooperative::active()->count(),
        ];

        // Compras recientes
        $recent_purchases = Purchase::with(['peasant', 'coffeeType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Ventas recientes
        $recent_sales = Sale::with(['cooperative', 'coffeeType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Datos para gráficas - últimos 6 meses
        $months = [];
        $purchases_data = [];
        $sales_data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $months[] = $monthName;
            
            // Compras del mes
            $purchases_sum = Purchase::where('status', 'completed')
                ->whereYear('purchase_date', $date->year)
                ->whereMonth('purchase_date', $date->month)
                ->sum('total');
            $purchases_data[] = (float) $purchases_sum;
            
            // Ventas del mes
            $sales_sum = Sale::where('status', 'completed')
                ->whereYear('sale_date', $date->year)
                ->whereMonth('sale_date', $date->month)
                ->sum('total');
            $sales_data[] = (float) $sales_sum;
        }

        return view('admin.dashboard', compact('stats', 'recent_purchases', 'recent_sales', 'months', 'purchases_data', 'sales_data'));
    }
}

