@extends('layouts.peasant')

@section('title', 'Mis Facturas - Campesino')
@section('page-title', 'Mis Facturas')

@section('content')
<div class="space-y-6" x-data="invoiceDetailModal()">
    <!-- Tabla de facturas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Café</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900">#{{ $invoice->invoice_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $invoice->purchase->coffeeType->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($invoice->purchase->quantity ?? 0, 2) }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                ${{ number_format($invoice->total, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $invoice->invoice_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            @click="openDetail({{ $invoice->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 rounded"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('peasant.invoices.download', $invoice) }}"
                                       class="text-green-600 hover:text-green-900 transition-colors" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file-invoice text-4xl mb-4 text-gray-300"></i>
                                <p>No tienes facturas registradas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detalle Factura -->
    <div x-show="open"
         x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto"
         aria-modal="true"
         role="dialog"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-black/50" @click="close()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden"
                 @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Detalle de factura</h2>
                    <button type="button"
                            @click="close()"
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-coffee-500">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <div x-html="content" x-show="content"></div>
                    <div x-show="loading" class="flex items-center justify-center py-12">
                        <i class="fas fa-spinner fa-spin text-3xl text-coffee-600"></i>
                    </div>
                    <div x-show="error" class="text-center py-8 text-red-600">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p>No se pudo cargar el detalle. Intenta de nuevo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function invoiceDetailModal() {
    return {
        open: false,
        content: '',
        loading: false,
        error: false,
        baseUrl: '{{ url("peasant/invoices") }}',
        openDetail(invoiceId) {
            this.open = true;
            this.content = '';
            this.error = false;
            this.loading = true;
            fetch(this.baseUrl + '/' + invoiceId + '/details', {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => { if (!r.ok) throw new Error(); return r.text(); })
                .then(html => { this.content = html; this.loading = false; })
                .catch(() => { this.error = true; this.loading = false; });
        },
        close() {
            this.open = false;
            this.content = '';
            this.error = false;
        }
    };
}
</script>
@endpush
@endsection

