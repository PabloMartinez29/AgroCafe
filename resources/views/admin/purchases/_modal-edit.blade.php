<!-- Modal para Editar Compra -->
<x-modal id="editPurchaseModal" title="Editar Compra" size="lg">
    <form id="editPurchaseForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select 
                label="Campesino" 
                name="peasant_id" 
                id="edit_purchase_peasant_id"
                required 
            >
                @foreach($peasants as $peasant)
                    <option value="{{ $peasant->id }}">{{ $peasant->name }}</option>
                @endforeach
            </x-select>

            <x-select 
                label="Tipo de Café" 
                name="coffee_type_id" 
                id="edit_purchase_coffee_type_id"
                required 
            >
                @foreach($coffee_types as $type)
                    @php
                        $bp = (float) $type->base_price;
                        $priceKg = $bp < 1000 ? $bp * 1000 : $bp;
                    @endphp
                    <option value="{{ $type->id }}" data-price="{{ $priceKg }}">
                        {{ $type->name }} - ${{ number_format($priceKg, 0, ',', '.') }}/kg
                    </option>
                @endforeach
            </x-select>

            <x-input 
                label="Cantidad (kg)" 
                name="quantity" 
                type="number" 
                step="0.01" 
                min="0.01"
                id="edit_purchase_quantity"
                required 
            />

            <x-input 
                label="Precio por kg (pesos)" 
                name="price_per_kg" 
                type="text" 
                inputmode="numeric"
                id="edit_purchase_price_per_kg"
                required 
                placeholder="Ej: 28000"
            />

            <div class="md:col-span-2">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-blue-900 mb-2">Total:</p>
                    <p class="text-2xl font-bold text-blue-600" id="edit_purchase_total">$0</p>
                </div>
            </div>

            <x-input 
                label="Fecha de Compra" 
                name="purchase_date" 
                type="date" 
                id="edit_purchase_date"
                required 
            />

            <x-select 
                label="Estado" 
                name="status" 
                id="edit_purchase_status"
                required 
            >
                <option value="pending">Pendiente</option>
                <option value="completed">Completada</option>
                <option value="cancelled">Cancelada</option>
            </x-select>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                <textarea 
                    name="observations" 
                    id="edit_purchase_observations"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-colors"
                ></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 mt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'editPurchaseModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Actualizar Compra
            </button>
        </div>
    </form>
</x-modal>

