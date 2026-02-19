<!-- Modal para Editar Venta -->
<x-modal id="editSaleModal" title="Editar Venta" size="lg">
    <form id="editSaleForm" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-select 
                label="Cooperativa (opcional)" 
                name="cooperative_id" 
                id="edit_sale_cooperative_id"
            >
                <option value="">Seleccione una cooperativa</option>
                @foreach($cooperatives as $cooperative)
                    <option value="{{ $cooperative->id }}">{{ $cooperative->name }}</option>
                @endforeach
            </x-select>

            <x-input 
                label="Nombre Cliente (si no es cooperativa)" 
                name="client_name" 
                id="edit_sale_client_name"
            />

            <x-select 
                label="Tipo de Café" 
                name="coffee_type_id" 
                id="edit_sale_coffee_type_id"
                required 
            >
                <option value="">Seleccione un tipo</option>
                @foreach($coffee_types_with_inventory as $type)
                    @php
                        $bp = (float) $type->base_price;
                        $priceKg = $bp < 1000 ? $bp * 1000 : $bp;
                    @endphp
                    <option value="{{ $type->id }}" data-price="{{ $priceKg }}">
                        {{ $type->name }} - ${{ number_format($priceKg, 0, ',', '.') }}/kg · Disponible: {{ number_format($type->available_inventory, 2, ',', '.') }} kg
                    </option>
                @endforeach
            </x-select>

            <x-input 
                label="Cantidad (kg)" 
                name="quantity" 
                type="number" 
                step="0.01" 
                min="0.01"
                id="edit_sale_quantity"
                required 
            />

            <x-input 
                label="Precio por kg (pesos)" 
                name="price_per_kg" 
                type="text" 
                inputmode="numeric"
                id="edit_sale_price_per_kg"
                required 
                placeholder="Ej: 28000"
            />

            <div class="md:col-span-2">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-purple-900 mb-2">Total:</p>
                    <p class="text-2xl font-bold text-purple-600" id="edit_sale_total">$0</p>
                </div>
            </div>

            <x-input 
                label="Fecha de Venta" 
                name="sale_date" 
                type="date" 
                id="edit_sale_date"
                required 
            />

            <x-select 
                label="Estado" 
                name="status" 
                id="edit_sale_status"
                required 
            >
                <option value="pending">Pendiente</option>
                <option value="completed">Completada</option>
                <option value="cancelled">Cancelada</option>
            </x-select>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 mt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'editSaleModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cancelar
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Actualizar Venta
            </button>
        </div>
    </form>
</x-modal>

