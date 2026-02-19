<!-- Modal para Editar Cooperativa -->
<x-modal id="editCooperativeModal" title="Editar Cooperativa" size="md">
    <form id="editCooperativeForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-4">
            <x-input 
                label="Nombre de la Cooperativa" 
                name="name" 
                id="edit_cooperative_name"
                required 
            />

            <x-input 
                label="NIT" 
                name="nit" 
                id="edit_cooperative_nit"
                required 
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input 
                    label="Teléfono" 
                    name="phone" 
                    id="edit_cooperative_phone"
                />

                <x-input 
                    label="Email" 
                    name="email" 
                    type="email" 
                    id="edit_cooperative_email"
                />
            </div>

            <x-input 
                label="Representante Legal" 
                name="legal_representative" 
                id="edit_cooperative_legal_representative"
            />

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                <textarea 
                    name="address" 
                    id="edit_cooperative_address"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors"
                ></textarea>
            </div>

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="active" 
                    id="edit_cooperative_active" 
                    value="1"
                    class="w-4 h-4 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                >
                <label for="edit_cooperative_active" class="ml-2 text-sm text-gray-700">Cooperativa activa</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <button type="button" 
                        @click="$dispatch('close-modal', { id: 'editCooperativeModal' }); document.body.style.overflow = ''"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Actualizar Cooperativa
                </button>
            </div>
        </div>
    </form>
</x-modal>

