@extends('layouts.admin')

@section('title', 'Compras - Administrador')
@section('page-title', 'Gestión de Compras')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Compras</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona las compras realizadas a campesinos</p>
        </div>
        <button onclick="openCreatePurchaseModal()" 
                class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nueva Compra</span>
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.purchases.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Campesino</label>
                <select name="peasant_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    <option value="">Todos los campesinos</option>
                    @foreach($peasants as $peasant)
                        <option value="{{ $peasant->id }}" {{ request('peasant_id') == $peasant->id ? 'selected' : '' }}>
                            {{ $peasant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Café</label>
                <select name="coffee_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    <option value="">Todos los tipos</option>
                    @foreach($coffee_types as $type)
                        <option value="{{ $type->id }}" {{ request('coffee_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Procesamiento</label>
                <select name="processing_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    <option value="">Todos</option>
                    <option value="normal" {{ request('processing_type') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="wet" {{ request('processing_type') === 'wet' ? 'selected' : '' }}>Mojado</option>
                    <option value="dry" {{ request('processing_type') === 'dry' ? 'selected' : '' }}>Seco</option>
                    <option value="pasilla" {{ request('processing_type') === 'pasilla' ? 'selected' : '' }}>Pasilla</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="px-3 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors"
                        title="Filtrar">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de compras -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="purchasesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campesino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Café</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio/kg</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-coffee-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $purchase->peasant->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $purchase->coffeeType->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($purchase->quantity, 2) }} kg</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${{ number_format($purchase->price_per_kg, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">${{ number_format($purchase->total, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $purchase->status === 'completed' ? 'bg-coffee-100 text-coffee-800' : ($purchase->status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900') }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewPurchaseModal('{{ $purchase->peasant->name }}', '{{ $purchase->coffeeType->name }}', {{ $purchase->quantity }}, {{ $purchase->price_per_kg }}, {{ $purchase->total }}, '{{ $purchase->status }}', '{{ $purchase->purchase_date->format('Y-m-d') }}', '{{ $purchase->purchase_date->format('d/m/Y') }}', '{{ $purchase->observations ?? '' }}')"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditPurchaseModal({{ $purchase->id }}, {{ $purchase->peasant_id }}, {{ $purchase->coffee_type_id }}, {{ $purchase->quantity }}, {{ (float)$purchase->price_per_kg < 1000 ? (float)$purchase->price_per_kg * 1000 : (float)$purchase->price_per_kg }}, '{{ $purchase->purchase_date->format('Y-m-d') }}', '{{ $purchase->status }}', '{{ $purchase->observations ?? '' }}')"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.purchases.destroy', $purchase) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta compra?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-600 transition-colors" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                            <td class="px-6 py-4 whitespace-nowrap"></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modales -->
@include('admin.purchases._modal-view')
@include('admin.purchases._modal-edit')

<!-- Modal para crear compra -->
<div id="createPurchaseModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 overflow-y-auto" onclick="closeModalOnBackdrop(event, 'createPurchaseModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header del formulario -->
        <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Nueva Compra
                    </h3>
                    <p class="text-coffee-100 mt-1">Completa el formulario para registrar una nueva compra</p>
                </div>
                <button onclick="closeCreatePurchaseModal()" class="text-white hover:text-coffee-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Contenido del formulario -->
        <form action="{{ route('admin.purchases.store') }}" method="POST" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-select 
                    label="Campesino" 
                    name="peasant_id" 
                    required 
                    :error="$errors->first('peasant_id')"
                >
                    <option value="">Seleccione un campesino</option>
                    @foreach($peasants as $peasant)
                        <option value="{{ $peasant->id }}" {{ old('peasant_id') == $peasant->id ? 'selected' : '' }}>
                            {{ $peasant->name }}
                        </option>
                    @endforeach
                </x-select>

                <x-select 
                    label="Tipo de Café" 
                    name="coffee_type_id" 
                    required 
                    :error="$errors->first('coffee_type_id')"
                    id="create_coffee_type_id"
                >
                    <option value="">Seleccione un tipo</option>
                    @foreach($coffee_types as $type)
                        @php
                            $bp = (float) $type->base_price;
                            $priceKg = $bp < 1000 ? $bp * 1000 : $bp;
                        @endphp
                        <option value="{{ $type->id }}" 
                                data-price="{{ $priceKg }}"
                                {{ old('coffee_type_id') == $type->id ? 'selected' : '' }}>
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
                    value="{{ old('quantity') }}" 
                    required 
                    id="create_quantity"
                    :error="$errors->first('quantity')"
                />

                <x-input 
                    label="Precio por kg (pesos)" 
                    name="price_per_kg" 
                    type="text" 
                    inputmode="numeric"
                    value="{{ old('price_per_kg') }}" 
                    required 
                    id="create_price_per_kg"
                    :error="$errors->first('price_per_kg')"
                    placeholder="Ej: 28000"
                />

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calculator mr-2 text-coffee-600"></i>
                        Total
                    </label>
                    <div class="bg-gradient-to-r from-coffee-50 to-coffee-100 border-2 border-coffee-200 rounded-xl p-6">
                        <p class="text-3xl font-bold text-coffee-700" id="create_total">$0</p>
                    </div>
                </div>

                <x-input 
                    label="Fecha de Compra" 
                    name="purchase_date" 
                    type="date" 
                    value="{{ old('purchase_date', date('Y-m-d')) }}" 
                    required 
                    :error="$errors->first('purchase_date')"
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
                    >{{ old('observations') }}</textarea>
                    @error('observations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                <button type="button" onclick="closeCreatePurchaseModal()" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Compra
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
    #purchasesTable_wrapper .dataTables_length,
    #purchasesTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #purchasesTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #purchasesTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #purchasesTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #purchasesTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #purchasesTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#purchasesTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#purchasesTable')) {
                $('#purchasesTable').DataTable().destroy();
            }
            
            // Inicializar DataTables
            table.DataTable({
                language: {
                    "decimal": ",",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "infoThousands": ".",
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros coincidentes",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "aria": {
                        "sortAscending": ": activar para ordenar columna ascendente",
                        "sortDescending": ": activar para ordenar columna descendente"
                    }
                },
                processing: false,
                serverSide: false,
                responsive: true,
                autoWidth: false,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[5, 'desc']], // Ordenar por fecha descendente (columna 5 = Fecha)
                columnDefs: [
                    { orderable: false, targets: 7 } // Deshabilitar ordenamiento en columna de acciones
                ],
                dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t><"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 space-y-2 sm:space-y-0"ip>',
                initComplete: function(settings, json) {
                    // Verificar que el número de columnas sea correcto
                    var api = this.api();
                    var columns = api.columns().count();
                    if (columns !== 8) {
                        console.warn('Número de columnas detectado:', columns, 'esperado: 8');
                    }
                }
            });
        }
    });

    function openViewPurchaseModal(peasant, coffeeType, quantity, pricePerKg, total, status, dateValue, dateFormatted, observations) {
        document.getElementById('view_purchase_peasant').textContent = peasant;
        document.getElementById('view_purchase_coffee_type').textContent = coffeeType;
        document.getElementById('view_purchase_quantity').textContent = parseFloat(quantity).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg';
        document.getElementById('view_purchase_price').textContent = '$' + parseFloat(pricePerKg).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_purchase_total').textContent = '$' + parseFloat(total).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_purchase_date').textContent = dateFormatted;
        
        const statusBadge = document.getElementById('view_purchase_status_badge');
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.className = 'inline-block px-4 py-2 text-sm font-semibold rounded-full ' + 
            (status === 'completed' ? 'bg-coffee-100 text-coffee-800' : 
             (status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900'));
        
        if (observations) {
            document.getElementById('view_purchase_observations').textContent = observations;
            document.getElementById('view_purchase_observations_container').style.display = 'block';
        } else {
            document.getElementById('view_purchase_observations_container').style.display = 'none';
        }
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewPurchaseModal' } }));
    }

    function openEditPurchaseModal(id, peasantId, coffeeTypeId, quantity, pricePerKg, date, status, observations) {
        document.getElementById('editPurchaseForm').action = '{{ route('admin.purchases.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_purchase_peasant_id').value = peasantId;
        document.getElementById('edit_purchase_coffee_type_id').value = coffeeTypeId;
        document.getElementById('edit_purchase_quantity').value = quantity;
        document.getElementById('edit_purchase_price_per_kg').value = pricePerKg;
        document.getElementById('edit_purchase_date').value = date;
        document.getElementById('edit_purchase_status').value = status;
        document.getElementById('edit_purchase_observations').value = observations || '';
        
        calculateEditPurchaseTotal();
        
        // Actualizar precio cuando cambia el tipo de café
        document.getElementById('edit_purchase_coffee_type_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                document.getElementById('edit_purchase_price_per_kg').value = price;
                calculateEditPurchaseTotal();
            }
        });
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'editPurchaseModal' } }));
    }

    function calculateEditPurchaseTotal() {
        const quantity = parseFloat(document.getElementById('edit_purchase_quantity').value) || 0;
        const price = parseFloat(document.getElementById('edit_purchase_price_per_kg').value) || 0;
        const total = quantity * price;
        document.getElementById('edit_purchase_total').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Funciones para el modal de crear compra
    function openCreatePurchaseModal() {
        const modal = document.getElementById('createPurchaseModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCreatePurchaseModal() {
        const modal = document.getElementById('createPurchaseModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        
        // Limpiar el formulario
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            document.getElementById('create_total').textContent = '$0';
        }
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            if (modalId === 'createPurchaseModal') {
                closeCreatePurchaseModal();
            }
        }
    }

    // Calcular total para el formulario de crear compra
    function calculateCreatePurchaseTotal() {
        const quantity = parseFloat(document.getElementById('create_quantity').value) || 0;
        const price = parseFloat(document.getElementById('create_price_per_kg').value) || 0;
        const total = quantity * price;
        document.getElementById('create_total').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Para editar compra
        const quantityInput = document.getElementById('edit_purchase_quantity');
        const priceInput = document.getElementById('edit_purchase_price_per_kg');
        
        if (quantityInput) {
            quantityInput.addEventListener('input', calculateEditPurchaseTotal);
        }
        if (priceInput) {
            priceInput.addEventListener('input', calculateEditPurchaseTotal);
        }

        // Para crear compra
        const createQuantityInput = document.getElementById('create_quantity');
        const createPriceInput = document.getElementById('create_price_per_kg');
        const createCoffeeTypeSelect = document.getElementById('create_coffee_type_id');
        
        if (createQuantityInput) {
            createQuantityInput.addEventListener('input', calculateCreatePurchaseTotal);
        }
        if (createPriceInput) {
            createPriceInput.addEventListener('input', calculateCreatePurchaseTotal);
        }
        if (createCoffeeTypeSelect) {
            createCoffeeTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                if (price) {
                    document.getElementById('create_price_per_kg').value = price;
                    calculateCreatePurchaseTotal();
                }
            });
        }

        // Abrir modal automáticamente si hay errores o si se solicita
        @if(session('open_create_modal') || $errors->any())
            openCreatePurchaseModal();
        @endif
    });
</script>
@endpush
@endsection
