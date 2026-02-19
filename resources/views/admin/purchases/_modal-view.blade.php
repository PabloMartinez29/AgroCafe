<!-- Modal para Ver Compra -->
<x-modal id="viewPurchaseModal" title="Detalles de la Compra" size="lg">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Campesino</p>
                <p class="text-lg font-semibold text-gray-900" id="view_purchase_peasant"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900" id="view_purchase_coffee_type"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900" id="view_purchase_quantity"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Precio por kg</p>
                <p class="text-lg font-semibold text-gray-900" id="view_purchase_price"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-green-600" id="view_purchase_total"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full" id="view_purchase_status_badge"></span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Fecha de Compra</p>
                <p class="text-lg font-semibold text-gray-900" id="view_purchase_date"></p>
            </div>
        </div>

        <div id="view_purchase_observations_container" style="display: none;">
            <p class="text-sm font-medium text-gray-500 mb-1">Observaciones</p>
            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg" id="view_purchase_observations"></p>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewPurchaseModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>

