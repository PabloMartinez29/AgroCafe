<!-- Modal para Ver Tipo de Café -->
<x-modal id="viewCoffeeTypeModal" title="Detalles del Tipo de Café" size="md">
    <div class="space-y-4">
        <div class="flex items-center space-x-4 pb-4 border-b border-gray-200">
            <div class="w-16 h-16 bg-coffee-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                    <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xl font-semibold text-gray-900" id="view_coffee_type_name"></h4>
                <p class="text-sm text-gray-600" id="view_coffee_type_variety_quality"></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Variedad</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800" id="view_coffee_type_variety"></span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Calidad</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full" id="view_coffee_type_quality_badge"></span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Procesamiento</p>
                <p class="text-gray-900" id="view_coffee_type_processing_type"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Precio Base</p>
                <p class="text-lg font-semibold text-coffee-600" id="view_coffee_type_base_price"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full" id="view_coffee_type_status_badge"></span>
            </div>
        </div>

        <div id="view_coffee_type_description_container" style="display: none;">
            <p class="text-sm font-medium text-gray-500 mb-1">Descripción</p>
            <p class="text-gray-900 bg-gray-50 p-3 rounded-lg" id="view_coffee_type_description"></p>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewCoffeeTypeModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>

