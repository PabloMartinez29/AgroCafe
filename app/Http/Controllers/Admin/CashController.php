<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashController extends Controller
{
    public function index()
    {
        $openCash = CashRegister::where('status', 'open')->first();
        $cashHistory = CashRegister::orderBy('opening_date', 'desc')->get();
        
        return view('admin.cash.index', compact('openCash', 'cashHistory'));
    }

    public function open(Request $request)
    {
        // Validar que no haya otra caja abierta
        $existingOpen = CashRegister::where('status', 'open')->first();
        if ($existingOpen) {
            return redirect()->route('admin.cash.index')
                ->with('error', 'Ya existe una caja abierta. Debe cerrarla antes de abrir una nueva.');
        }

        $validated = $request->validate([
            'register_name' => 'required|string|max:100',
            'initial_amount' => 'required|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',
            'opening_date' => 'required|date',
            'opening_time' => 'required|string',
        ]);

        $openingDateTime = Carbon::parse($validated['opening_date'] . ' ' . $validated['opening_time']);

        CashRegister::create([
            'register_name' => $validated['register_name'],
            'initial_amount' => $validated['initial_amount'],
            'base_salary' => $validated['base_salary'],
            'available_balance' => $validated['base_salary'],
            'opening_date' => $openingDateTime,
            'kilos_purchased' => 0,
            'kilos_sold' => 0,
            'operating_hours' => 0,
            'status' => 'open',
        ]);

        return redirect()->route('admin.cash.index')
            ->with('success', "Caja '{$validated['register_name']}' abierta exitosamente.");
    }

    public function close(Request $request)
    {
        $openCash = CashRegister::where('status', 'open')->first();
        
        if (!$openCash) {
            return redirect()->route('admin.cash.index')
                ->with('error', 'No hay una caja abierta para cerrar.');
        }

        // Calcular horas de operación
        $openingTime = Carbon::parse($openCash->opening_date);
        $closingTime = Carbon::now();
        $hoursDiff = $openingTime->diffInHours($closingTime);
        $minutesDiff = $openingTime->diffInMinutes($closingTime) % 60;
        $totalHours = round($openingTime->diffInMinutes($closingTime) / 60, 2);

        $openCash->update([
            'status' => 'closed',
            'closing_date' => $closingTime,
            'operating_hours' => $totalHours,
        ]);

        $timeText = "{$hoursDiff}h {$minutesDiff}m";
        
        return redirect()->route('admin.cash.index')
            ->with('success', "Caja '{$openCash->register_name}' cerrada exitosamente. Tiempo operativo: {$timeText}");
    }

    /**
     * Obtener la caja abierta actual
     */
    public static function getOpenCash()
    {
        return CashRegister::where('status', 'open')->first();
    }

    /**
     * Verificar si hay caja abierta
     */
    public static function hasOpenCash()
    {
        return CashRegister::where('status', 'open')->exists();
    }

    /**
     * Actualizar saldo de caja después de una compra
     */
    public static function updateBalanceAfterPurchase($amount, $kilos)
    {
        $openCash = self::getOpenCash();
        if ($openCash) {
            $openCash->update([
                'available_balance' => $openCash->available_balance - $amount,
                'kilos_purchased' => $openCash->kilos_purchased + $kilos,
            ]);
        }
    }
}
