<!-- Modal para Ver Venta -->
<x-modal id="viewSaleModal" title="Detalles de la Venta" size="lg">
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cliente</p>
                <p class="text-lg font-semibold text-gray-900" id="view_sale_client"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900" id="view_sale_coffee_type"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900" id="view_sale_quantity"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Precio por kg</p>
                <p class="text-lg font-semibold text-gray-900" id="view_sale_price"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-purple-600" id="view_sale_total"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full" id="view_sale_status_badge"></span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Fecha de Venta</p>
                <p class="text-lg font-semibold text-gray-900" id="view_sale_date"></p>
            </div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewSaleModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>

