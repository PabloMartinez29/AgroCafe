<!-- Modal para Ver Cooperativa -->
<x-modal id="viewCooperativeModal" title="Detalles de la Cooperativa" size="md">
    <div class="space-y-4">
        <div class="flex items-center space-x-4 pb-4 border-b border-gray-200">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-blue-600 text-2xl"></i>
            </div>
            <div>
                <h4 class="text-xl font-semibold text-gray-900" id="view_cooperative_name"></h4>
                <p class="text-sm text-gray-600" id="view_cooperative_nit"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Teléfono</p>
                <p class="text-gray-900" id="view_cooperative_phone">-</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Email</p>
                <p class="text-gray-900" id="view_cooperative_email">-</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Representante Legal</p>
                <p class="text-gray-900" id="view_cooperative_legal_representative">-</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado</p>
                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full" id="view_cooperative_status_badge"></span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Fecha de Registro</p>
                <p class="text-gray-900" id="view_cooperative_created_at"></p>
            </div>
        </div>

        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">Dirección</p>
            <p class="text-gray-900" id="view_cooperative_address">-</p>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewCooperativeModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</x-modal>

