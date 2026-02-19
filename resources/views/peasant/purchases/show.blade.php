@extends('layouts.peasant')

@section('title', 'Detalle de Compra - Campesino')
@section('page-title', 'Detalle de Compra')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Información Principal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Información de la Compra</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900">{{ $purchase->coffeeType->name }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ ucfirst($purchase->coffeeType->variety) }} - {{ ucfirst($purchase->coffeeType->quality) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($purchase->quantity, 2) }} kg</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Precio por kg</p>
                <p class="text-lg font-semibold text-gray-900">${{ number_format($purchase->price_per_kg, 0) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($purchase->total, 0) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($purchase->status) }}
                </span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Fecha de Compra</p>
                <p class="text-lg font-semibold text-gray-900">{{ $purchase->purchase_date->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Pagos -->
    @if($purchase->payments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pagos Recibidos</h3>
            <div class="space-y-3">
                @foreach($purchase->payments as $payment)
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="font-medium text-gray-900">${{ number_format($payment->amount, 0) }}</p>
                            <p class="text-sm text-gray-600">{{ ucfirst($payment->payment_method) }} - {{ $payment->payment_date->format('d/m/Y') }}</p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Factura -->
    @if($purchase->invoice)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Factura</h3>
                    <p class="text-sm text-gray-600 mt-1">N° {{ $purchase->invoice->invoice_number }}</p>
                </div>
                <a href="{{ route('peasant.invoices.show', $purchase->invoice) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-invoice mr-2"></i> Ver Factura
                </a>
            </div>
        </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('peasant.purchases.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Mis Compras
        </a>
    </div>
</div>
@endsection

