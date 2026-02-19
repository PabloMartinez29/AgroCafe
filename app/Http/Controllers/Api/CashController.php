<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $openCash = CashRegister::open()->first();
        $cashHistory = CashRegister::orderBy('opening_date', 'desc')->get();

        return response()->json([
            'open_cash' => $openCash,
            'history' => $cashHistory,
        ]);
    }

    public function open(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'register_name' => 'required|string|max:100',
            'initial_amount' => 'required|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',
            'opening_date' => 'required|date',
            'opening_time' => 'required|date_format:H:i',
        ]);

        if (CashRegister::open()->exists()) {
            return response()->json([
                'message' => 'Ya existe una caja abierta. Ciérrala antes de abrir una nueva.'
            ], 400);
        }

        $openingDateTime = Carbon::parse($request->opening_date . ' ' . $request->opening_time);

        $cashRegister = CashRegister::create([
            'register_name' => $request->register_name,
            'initial_amount' => $request->initial_amount,
            'base_salary' => $request->base_salary,
            'available_balance' => $request->base_salary,
            'opening_date' => $openingDateTime,
            'status' => 'open',
        ]);

        return response()->json($cashRegister, 201);
    }

    public function close(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $openCash = CashRegister::open()->first();

        if (!$openCash) {
            return response()->json([
                'message' => 'No hay una caja abierta para cerrar.'
            ], 400);
        }

        $openingTime = Carbon::parse($openCash->opening_date);
        $closingTime = Carbon::now();
        $operatingHours = $openingTime->diffInMinutes($closingTime) / 60;

        $openCash->update([
            'status' => 'closed',
            'closing_date' => $closingTime,
            'operating_hours' => round($operatingHours, 2),
        ]);

        return response()->json($openCash);
    }
}

