@extends('layouts.admin')

@section('title', 'Detalle de Factura - Administrador')
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
            <div class="flex items-center space-x-2">
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $invoice->transaction_type === 'purchase' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ $invoice->transaction_type === 'purchase' ? 'Compra' : 'Venta' }}
                </span>
                <a href="{{ route('admin.invoices.generate', $invoice) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">
                    {{ $invoice->transaction_type === 'purchase' ? 'Campesino' : 'Cliente' }}
                </p>
                <p class="text-lg font-semibold text-gray-900">
                    @if($invoice->transaction_type === 'purchase')
                        {{ $invoice->purchase->peasant->name ?? '-' }}
                    @else
                        {{ $invoice->sale->cooperative->name ?? $invoice->sale->client_name ?? '-' }}
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900">
                    @if($invoice->transaction_type === 'purchase')
                        {{ $invoice->purchase->coffeeType->name ?? '-' }}
                    @else
                        {{ $invoice->sale->coffeeType->name ?? '-' }}
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900">
                    @if($invoice->transaction_type === 'purchase')
                        {{ number_format($invoice->purchase->quantity ?? 0, 2) }} kg
                    @else
                        {{ number_format($invoice->sale->quantity ?? 0, 2) }} kg
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-coffee-600">${{ number_format($invoice->total, 0) }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado de Pago</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
                </span>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('admin.invoices.index') }}" 
           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Volver a Facturas
        </a>
    </div>
</div>
@endsection

