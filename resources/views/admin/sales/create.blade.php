@extends('layouts.admin')

@section('title', 'Nueva Venta - Administrador')
@section('page-title', 'Registrar Nueva Venta')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-handshake mr-3"></i>
                    Nueva Venta
                </h3>
                <p class="text-coffee-100 mt-1">Completa el formulario para registrar una nueva venta</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.sales.store') }}" method="POST" class="p-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-select 
                        label="Cooperativa (opcional)" 
                        name="cooperative_id" 
                        :error="$errors->first('cooperative_id')"
                    >
                        <option value="">Seleccione una cooperativa</option>
                        @foreach($cooperatives as $cooperative)
                            <option value="{{ $cooperative->id }}" {{ old('cooperative_id') == $cooperative->id ? 'selected' : '' }}>
                                {{ $cooperative->name }}
                            </option>
                        @endforeach
                    </x-select>

                    <x-input 
                        label="Nombre Cliente (si no es cooperativa)" 
                        name="client_name" 
                        value="{{ old('client_name') }}" 
                        :error="$errors->first('client_name')"
                    />

                    <x-select 
                        label="Tipo de Café" 
                        name="coffee_type_id" 
                        required 
                        :error="$errors->first('coffee_type_id')"
                    >
                        <option value="">Seleccione un tipo</option>
                        @foreach($coffee_types_with_inventory as $type)
                            @php
                                $bp = (float) $type->base_price;
                                $priceKg = $bp < 1000 ? $bp * 1000 : $bp;
                            @endphp
                            <option value="{{ $type->id }}" 
                                    data-price="{{ $priceKg }}"
                                    {{ old('coffee_type_id') == $type->id ? 'selected' : '' }}>
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
                        value="{{ old('quantity') }}" 
                        required 
                        id="quantity"
                        :error="$errors->first('quantity')"
                    />

                    <x-input 
                        label="Precio por kg (pesos)" 
                        name="price_per_kg" 
                        type="text" 
                        inputmode="numeric"
                        value="{{ old('price_per_kg') }}" 
                        required 
                        id="price_per_kg"
                        :error="$errors->first('price_per_kg')"
                        placeholder="Ej: 28000"
                    />

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calculator mr-2 text-coffee-600"></i>
                            Total
                        </label>
                        <div class="bg-gradient-to-r from-coffee-50 to-coffee-100 border-2 border-coffee-200 rounded-xl p-6">
                            <p class="text-3xl font-bold text-coffee-700" id="total">$0</p>
                        </div>
                    </div>

                    <x-input 
                        label="Fecha de Venta" 
                        name="sale_date" 
                        type="date" 
                        value="{{ old('sale_date', date('Y-m-d')) }}" 
                        required 
                        :error="$errors->first('sale_date')"
                    />

                    <x-select 
                        label="Estado" 
                        name="status" 
                        required 
                        :error="$errors->first('status')"
                    >
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </x-select>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.sales.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Venta
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
