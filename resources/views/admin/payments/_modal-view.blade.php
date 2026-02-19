<!-- Modal para Ver Pago -->
<x-modal id="viewPaymentModal" title="Detalles del Pago" size="md">
    <div class="space-y-4">
        <div class="pb-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-900" id="view_payment_type"></h4>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cliente/Campesino</p>
                <p class="text-lg font-semibold text-gray-900" id="view_payment_client"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Monto</p>
                <p class="text-2xl font-bold text-green-600" id="view_payment_amount"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Método de Pago</p>
                <p class="text-gray-900" id="view_payment_method"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Referencia</p>
                <p class="text-gray-900" id="view_payment_reference"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Fecha</p>
                <p class="text-gray-900" id="view_payment_date"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full" id="view_payment_status_badge"></span>
            </div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewPaymentModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>

