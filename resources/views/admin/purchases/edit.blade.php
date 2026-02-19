@extends('layouts.admin')

@section('title', 'Editar Compra - Administrador')
@section('page-title', 'Editar Compra')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    Editar Compra
                </h3>
                <p class="text-coffee-100 mt-1">Modifica la información de la compra</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.purchases.update', $purchase) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-select 
                        label="Campesino" 
                        name="peasant_id" 
                        required 
                        :error="$errors->first('peasant_id')"
                    >
                        @foreach($peasants as $peasant)
                            <option value="{{ $peasant->id }}" {{ old('peasant_id', $purchase->peasant_id) == $peasant->id ? 'selected' : '' }}>
                                {{ $peasant->name }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-select 
                        label="Tipo de Café" 
                        name="coffee_type_id" 
                        required 
                        :error="$errors->first('coffee_type_id')"
                    >
                        @foreach($coffee_types as $type)
                            <option value="{{ $type->id }}" 
                                    data-price="{{ $type->base_price }}"
                                    {{ old('coffee_type_id', $purchase->coffee_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} - ${{ number_format($type->base_price, 0) }}/kg
                            </option>
                        @endforeach
                    </x-select>

                    <x-input 
                        label="Cantidad (kg)" 
                        name="quantity" 
                        type="number" 
                        step="0.01" 
                        min="0.01"
                        value="{{ old('quantity', $purchase->quantity) }}" 
                        required 
                        id="quantity"
                        :error="$errors->first('quantity')"
                    />

                    <x-input 
                        label="Precio por kg" 
                        name="price_per_kg" 
                        type="number" 
                        step="0.01" 
                        min="0"
                        value="{{ old('price_per_kg', $purchase->price_per_kg) }}" 
                        required 
                        id="price_per_kg"
                        :error="$errors->first('price_per_kg')"
                    />

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calculator mr-2 text-coffee-600"></i>
                            Total
                        </label>
                        <div class="bg-gradient-to-r from-coffee-50 to-coffee-100 border-2 border-coffee-200 rounded-xl p-6">
                            <p class="text-3xl font-bold text-coffee-700" id="total">${{ number_format($purchase->total, 0) }}</p>
                        </div>
                    </div>

                    <x-input 
                        label="Fecha de Compra" 
                        name="purchase_date" 
                        type="date" 
                        value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" 
                        required 
                        :error="$errors->first('purchase_date')"
                    />

                    <x-select 
                        label="Estado" 
                        name="status" 
                        required 
                        :error="$errors->first('status')"
                    >
                        <option value="pending" {{ old('status', $purchase->status) === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="completed" {{ old('status', $purchase->status) === 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ old('status', $purchase->status) === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </x-select>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-comment-alt mr-2 text-coffee-600"></i>
                            Observaciones
                        </label>
                        <textarea 
                            name="observations" 
                            rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500 transition-all resize-none"
                            placeholder="Ingrese observaciones adicionales (opcional)"
                        >{{ old('observations', $purchase->observations) }}</textarea>
                        @error('observations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.purchases.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price_per_kg');
        const coffeeTypeSelect = document.querySelector('select[name="coffee_type_id"]');
        const totalDisplay = document.getElementById('total');

        function calculateTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalDisplay.textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        coffeeTypeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                priceInput.value = price;
                calculateTotal();
            }
        });

        quantityInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
    });
</script>
@endpush
@endsection
