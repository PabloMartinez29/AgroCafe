<?php

namespace App\Http\Controllers\Peasant;

use App\Http\Controllers\Controller;
use App\Models\CoffeeType;
use App\Models\HistoricalPrice;
use Illuminate\Http\Request;

class PriceAnalysisController extends Controller
{
    public function index(Request $request)
    {
        // Reutilizar la misma lógica que el administrador: ver todos los tipos de café activos
        $coffee_types = CoffeeType::active()->get();

        if ($coffee_types->isEmpty()) {
            return view('peasant.price-analysis.index', [
                'coffee_types' => collect(),
                'coffee_types_data' => [],
                'chart_labels' => [],
                'all_dates' => [],
                'historical_prices_list' => [],
            ]);
        }

        $all_historical_prices = HistoricalPrice::where('price_date', '>=', now()->subDays(30))
            ->orderBy('price_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Generar todas las fechas de los últimos 30 días (eje X fijo)
        $all_dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $all_dates[$date] = $date;
        }

        $coffee_types_data = [];
        $historical_prices_list = [];

        foreach ($coffee_types as $type) {
            $type_prices = $all_historical_prices->where('coffee_type_id', $type->id);

            $latest_price = HistoricalPrice::where('coffee_type_id', $type->id)
                ->orderBy('price_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            $base_price = (float) $type->base_price;
            $market_base = $latest_price ? (float) $latest_price->price : ($base_price > 0 ? $base_price : 20000);
            $current_price = $market_base;

            $price_change = null;
            $price_change_percent = null;
            if ($latest_price) {
                $price_24h_ago = HistoricalPrice::where('coffee_type_id', $type->id)
                    ->where('price_date', '<', $latest_price->price_date)
                    ->orderBy('price_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($price_24h_ago) {
                    $price_change = $current_price - (float) $price_24h_ago->price;
                    $price_change_percent = ($price_change / (float) $price_24h_ago->price) * 100;
                }
            }

            $grouped_prices = $type_prices->groupBy(function ($price) {
                return $price->price_date->format('Y-m-d');
            });

            $prices_by_date = [];

            foreach ($all_dates as $date) {
                if (isset($grouped_prices[$date])) {
                    $last_price_of_day = $grouped_prices[$date]->last();
                    $price_for_day = (float) $last_price_of_day->price;
                    $prices_by_date[$date] = $price_for_day;

                    $historical_prices_list[] = [
                        'date' => $date,
                        'coffee_type' => $type->name,
                        'processing_type' => $type->processing_type,
                        'price' => $price_for_day,
                        'operation_type' => $last_price_of_day->operation_type,
                    ];
                } else {
                    $noise = rand(-200, 200) / 1000; // ±20%
                    $price_for_day = max(0.01, round($market_base * (1 + $noise), 2));
                    $prices_by_date[$date] = $price_for_day;
                }
            }

            if ($price_change === null && count($prices_by_date) > 1) {
                $first_price = reset($prices_by_date);
                $last_price_calc = end($prices_by_date);
                $price_change = $last_price_calc - $first_price;
                $price_change_percent = ($first_price > 0) ? ($price_change / $first_price) * 100 : 0;
            }

            $coffee_types_data[] = [
                'id' => $type->id,
                'name' => $type->name,
                'variety' => $type->variety,
                'quality' => $type->quality,
                'processing_type' => $type->processing_type,
                'current_price' => $current_price,
                'price_change' => $price_change,
                'price_change_percent' => $price_change_percent,
                'prices_by_date' => $prices_by_date,
            ];
        }

        ksort($all_dates);
        $chart_labels = array_map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('d/m');
        }, array_values($all_dates));

        usort($historical_prices_list, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return view('peasant.price-analysis.index', compact(
            'coffee_types',
            'coffee_types_data',
            'chart_labels',
            'all_dates',
            'historical_prices_list'
        ));
    }

    public function getRealTimeData(Request $request)
    {
        $coffee_types = CoffeeType::active()->get();

        $all_historical_prices = HistoricalPrice::where('price_date', '>=', now()->subDays(30))
            ->orderBy('price_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Eje X fijo: últimos 30 días
        $all_dates = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $all_dates[] = $date;
        }

        $labels = array_map(function ($date) {
            return \Carbon\Carbon::parse($date)->format('d/m');
        }, $all_dates);

        $datasets = [];
        $colors = [
            ['border' => '#dc2626', 'bg' => 'rgba(220, 38, 38, 0.1)'],
            ['border' => '#7a5f47', 'bg' => 'rgba(122, 95, 71, 0.1)'],
            ['border' => '#16a34a', 'bg' => 'rgba(22, 163, 74, 0.1)'],
            ['border' => '#ea580c', 'bg' => 'rgba(234, 88, 12, 0.1)'],
            ['border' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.1)'],
            ['border' => '#ec4899', 'bg' => 'rgba(236, 72, 153, 0.1)'],
            ['border' => '#14b8a6', 'bg' => 'rgba(20, 184, 166, 0.1)'],
            ['border' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)'],
        ];

        $color_index = 0;

        foreach ($coffee_types as $type) {
            $type_prices = $all_historical_prices->where('coffee_type_id', $type->id);

            $grouped_prices = $type_prices->groupBy(function ($price) {
                return $price->price_date->format('Y-m-d');
            });

            $prices_data = [];

            // Base de mercado: último precio real o base del tipo
            $latest_price = HistoricalPrice::where('coffee_type_id', $type->id)
                ->orderBy('price_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            $base_price = (float) $type->base_price;
            $market_base = $latest_price ? (float) $latest_price->price : ($base_price > 0 ? $base_price : 20000);

            foreach ($all_dates as $date) {
                if (isset($grouped_prices[$date])) {
                    $price_for_day = (float) $grouped_prices[$date]->last()->price;
                } else {
                    $noise = rand(-200, 200) / 1000; // ±20%
                    $price_for_day = max(0.01, round($market_base * (1 + $noise), 2));
                }

                $prices_data[] = $price_for_day;
            }

            $current_price = end($prices_data) ?: $market_base;

            $price_change = null;
            $price_change_percent = null;
            if (count($prices_data) > 1) {
                $last_price_value = end($prices_data);
                $prev_price = $prices_data[count($prices_data) - 2];
                $price_change = $last_price_value - $prev_price;
                $price_change_percent = ($prev_price > 0) ? ($price_change / $prev_price) * 100 : 0;
            }

            $color = $colors[$color_index % count($colors)];
            $color_index++;

            $display_name = $type->name . ' (' . \App\Models\CoffeeType::translateProcessingType($type->processing_type) . ')';

            $datasets[] = [
                'label' => $display_name,
                'data' => $prices_data,
                'borderColor' => $color['border'],
                'backgroundColor' => $color['bg'],
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
                'pointRadius' => 2,
                'pointHoverRadius' => 4,
                'pointBackgroundColor' => $color['border'],
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 1,
                'current_price' => $current_price,
                'coffee_type_id' => $type->id,
                'price_change' => $price_change,
                'price_change_percent' => $price_change_percent,
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}


