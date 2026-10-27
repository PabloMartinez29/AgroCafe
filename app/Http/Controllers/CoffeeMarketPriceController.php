<?php

namespace App\Http\Controllers;

use App\Services\ColombiaCoffeeMarket;
use Illuminate\Http\JsonResponse;

class CoffeeMarketPriceController extends Controller
{
    /**
     * Precio vigente del café verde, seco y pasilla en Colombia.
     */
    public function __invoke(ColombiaCoffeeMarket $market): JsonResponse
    {
        $prices = $market->current();

        if ($prices === null) {
            return response()->json([
                'message' => 'No fue posible consultar el precio del mercado.',
            ], 503);
        }

        return response()->json($prices)->header('Cache-Control', 'no-store');
    }
}
