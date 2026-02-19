@extends('layouts.peasant')

@section('title', 'Detalle de Factura - Campesino')
@section('page-title', 'Detalle de Factura')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Información Principal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Factura #{{ $invoice->invoice_number }}</h3>
                <p class="text-sm text-gray-600 mt-1">Fecha: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    Compra
                </span>
                <a href="{{ route('peasant.invoices.download', $invoice) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900">{{ $invoice->purchase->coffeeType->name ?? '-' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900">{{ number_format($invoice->purchase->quantity ?? 0, 2) }} kg</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Precio por kg</p>
                <p class="text-lg font-semibold text-gray-900">${{ number_format($invoice->purchase->price_per_kg ?? 0, 0) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($invoice->total, 0) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado de Pago</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Pagos Recibidos -->
    @if($invoice->purchase->payments->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pagos Recibidos</h3>
            <div class="space-y-3">
                @foreach($invoice->purchase->payments as $payment)
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

    <div class="flex justify-end">
        <a href="{{ route('peasant.invoices.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Mis Facturas
        </a>
    </div>
</div>
@endsection

