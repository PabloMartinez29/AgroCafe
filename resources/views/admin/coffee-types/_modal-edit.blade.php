<!-- Modal para Editar Tipo de Café -->
<x-modal id="editCoffeeTypeModal" title="Editar Tipo de Café" size="md">
    <form id="editCoffeeTypeForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-4">
            <x-input 
                label="Nombre del Tipo de Café" 
                name="name" 
                id="edit_coffee_type_name"
                required 
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-select 
                    label="Variedad" 
                    name="variety" 
                    id="edit_coffee_type_variety"
                    required 
                >
                    <option value="arabica">Arábica</option>
                    <option value="robusta">Robusta</option>
                </x-select>

                <x-select 
                    label="Calidad" 
                    name="quality" 
                    id="edit_coffee_type_quality"
                    required 
                >
                    <option value="premium">Premium</option>
                    <option value="special">Especial</option>
                    <option value="commercial">Comercial</option>
                </x-select>

                <x-select 
                    label="Tipo de Procesamiento" 
                    name="processing_type" 
                    id="edit_coffee_type_processing_type"
                    required 
                >
                    <option value="normal">Normal</option>
                    <option value="wet">Mojado</option>
                    <option value="dry">Seco</option>
                    <option value="pasilla">Pasilla</option>
                </x-select>

                <x-input 
                    label="Precio Base (por kg)" 
                    name="base_price" 
                    type="number" 
                    step="0.01" 
                    min="0"
                    id="edit_coffee_type_base_price"
                    required 
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea 
                    name="description" 
                    id="edit_coffee_type_description"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors"
                ></textarea>
            </div>

            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    name="active" 
                    id="edit_coffee_type_active" 
                    value="1"
                    class="w-4 h-4 text-coffee-600 border-gray-300 rounded focus:ring-coffee-500"
                >
                <label for="edit_coffee_type_active" class="ml-2 text-sm text-gray-700">Tipo de café activo</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <button type="button" 
                        @click="$dispatch('close-modal', { id: 'editCoffeeTypeModal' }); document.body.style.overflow = ''"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Actualizar Tipo de Café
                </button>
            </div>
        </div>
    </form>
</x-modal>

