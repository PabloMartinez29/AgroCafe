{{-- Contenido del modal de detalle de factura (panel campesino) --}}
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Factura #{{ $invoice->invoice_number }}</h3>
            <p class="text-sm text-gray-600 mt-0.5">Fecha: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 text-sm font-semibold rounded-full bg-coffee-100 text-coffee-800">Compra</span>
            <a href="{{ route('peasant.invoices.download', $invoice) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors inline-flex items-center text-sm font-medium">
                <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tipo de Café</p>
            <p class="text-base font-semibold text-gray-900">{{ $invoice->purchase->coffeeType->name ?? '-' }}</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cantidad</p>
            <p class="text-base font-semibold text-gray-900">{{ number_format($invoice->purchase->quantity ?? 0, 2) }} kg</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Precio por kg</p>
            <p class="text-base font-semibold text-gray-900">${{ number_format($invoice->purchase->price_per_kg ?? 0, 0) }}</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total</p>
            <p class="text-lg font-bold text-green-600">${{ number_format($invoice->total, 0) }}</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 sm:col-span-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Estado de Pago</p>
            <span class="inline-block px-3 py-1.5 text-sm font-semibold rounded-full {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
            </span>
        </div>
    </div>

    @if($invoice->purchase->payments && $invoice->purchase->payments->count() > 0)
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Pagos Recibidos</h4>
            <div class="space-y-2 max-h-40 overflow-y-auto">
                @foreach($invoice->purchase->payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="font-medium text-gray-900">${{ number_format($payment->amount, 0) }}</p>
                            <p class="text-xs text-gray-600">{{ ucfirst($payment->payment_method) }} · {{ $payment->payment_date->format('d/m/Y') }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ ucfirst($payment->status) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
