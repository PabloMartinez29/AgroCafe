@extends('layouts.admin')

@section('title', 'Ventas - Administrador')
@section('page-title', 'Gestión de Ventas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Ventas</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona las ventas realizadas a cooperativas</p>
        </div>
        <button onclick="openCreateSaleModal()" 
                class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nueva Venta</span>
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.sales.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cooperativa</label>
                <select name="cooperative_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    <option value="">Todas las cooperativas</option>
                    @foreach($cooperatives as $cooperative)
                        <option value="{{ $cooperative->id }}" {{ request('cooperative_id') == $cooperative->id ? 'selected' : '' }}>
                            {{ $cooperative->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Café</label>
                <select name="coffee_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    <option value="">Todos los tipos</option>
                    @foreach($coffee_types_with_inventory as $type)
                        <option value="{{ $type->id }}" {{ request('coffee_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
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

    <!-- Tabla de ventas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="salesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
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
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-building text-coffee-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">
                                        {{ $sale->cooperative ? $sale->cooperative->name : $sale->client_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sale->coffeeType->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($sale->quantity, 2) }} kg</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${{ number_format($sale->price_per_kg, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">${{ number_format($sale->total, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $sale->status === 'completed' ? 'bg-coffee-100 text-coffee-800' : ($sale->status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900') }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewSaleModal('{{ $sale->cooperative ? $sale->cooperative->name : $sale->client_name }}', '{{ $sale->coffeeType->name }}', {{ $sale->quantity }}, {{ $sale->price_per_kg }}, {{ $sale->total }}, '{{ $sale->status }}', '{{ $sale->sale_date->format('d/m/Y') }}')"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditSaleModal({{ $sale->id }}, {{ $sale->cooperative_id ?? 'null' }}, '{{ $sale->client_name ?? '' }}', {{ $sale->coffee_type_id }}, {{ $sale->quantity }}, {{ (float)$sale->price_per_kg < 1000 ? (float)$sale->price_per_kg * 1000 : (float)$sale->price_per_kg }}, '{{ $sale->sale_date->format('Y-m-d') }}', '{{ $sale->status }}')"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.sales.destroy', $sale) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta venta?');">
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
@include('admin.sales._modal-view')
@include('admin.sales._modal-edit')

<!-- Modal para crear venta -->
<div id="createSaleModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 overflow-y-auto" onclick="closeModalOnBackdrop(event, 'createSaleModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Header del formulario -->
        <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-handshake mr-3"></i>
                        Nueva Venta
                    </h3>
                    <p class="text-coffee-100 mt-1">Completa el formulario para registrar una nueva venta</p>
                </div>
                <button onclick="closeCreateSaleModal()" class="text-white hover:text-coffee-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Contenido del formulario -->
        <form action="{{ route('admin.sales.store') }}" method="POST" class="p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-select 
                    label="Cooperativa (opcional)" 
                    name="cooperative_id" 
                    :error="$errors->first('cooperative_id')"
                    id="create_sale_cooperative_id"
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
                    id="create_sale_client_name"
                    :error="$errors->first('client_name')"
                />

                <x-select 
                    label="Tipo de Café" 
                    name="coffee_type_id" 
                    required 
                    :error="$errors->first('coffee_type_id')"
                    id="create_sale_coffee_type_id"
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
                    id="create_sale_quantity"
                    :error="$errors->first('quantity')"
                />

                <x-input 
                    label="Precio por kg (pesos)" 
                    name="price_per_kg" 
                    type="text" 
                    inputmode="numeric"
                    value="{{ old('price_per_kg') }}" 
                    required 
                    id="create_sale_price_per_kg"
                    :error="$errors->first('price_per_kg')"
                    placeholder="Ej: 28000"
                />

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calculator mr-2 text-coffee-600"></i>
                        Total
                    </label>
                    <div class="bg-gradient-to-r from-coffee-50 to-coffee-100 border-2 border-coffee-200 rounded-xl p-6">
                        <p class="text-3xl font-bold text-coffee-700" id="create_sale_total">$0</p>
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
                <button type="button" onclick="closeCreateSaleModal()" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Venta
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
    #salesTable_wrapper .dataTables_length,
    #salesTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #salesTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #salesTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #salesTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #salesTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #salesTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#salesTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#salesTable')) {
                $('#salesTable').DataTable().destroy();
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

    function openViewSaleModal(client, coffeeType, quantity, pricePerKg, total, status, dateFormatted) {
        document.getElementById('view_sale_client').textContent = client;
        document.getElementById('view_sale_coffee_type').textContent = coffeeType;
        document.getElementById('view_sale_quantity').textContent = parseFloat(quantity).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg';
        document.getElementById('view_sale_price').textContent = '$' + parseFloat(pricePerKg).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_sale_total').textContent = '$' + parseFloat(total).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_sale_date').textContent = dateFormatted;
        
        const statusBadge = document.getElementById('view_sale_status_badge');
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.className = 'inline-block px-4 py-2 text-sm font-semibold rounded-full ' + 
            (status === 'completed' ? 'bg-coffee-100 text-coffee-800' : 
             (status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900'));
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewSaleModal' } }));
    }

    function openEditSaleModal(id, cooperativeId, clientName, coffeeTypeId, quantity, pricePerKg, date, status) {
        document.getElementById('editSaleForm').action = '{{ route('admin.sales.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_sale_cooperative_id').value = cooperativeId || '';
        document.getElementById('edit_sale_client_name').value = clientName || '';
        document.getElementById('edit_sale_coffee_type_id').value = coffeeTypeId;
        document.getElementById('edit_sale_quantity').value = quantity;
        document.getElementById('edit_sale_price_per_kg').value = pricePerKg;
        document.getElementById('edit_sale_date').value = date;
        document.getElementById('edit_sale_status').value = status;
        
        calculateEditSaleTotal();
        
        // Actualizar precio cuando cambia el tipo de café
        document.getElementById('edit_sale_coffee_type_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                document.getElementById('edit_sale_price_per_kg').value = price;
                calculateEditSaleTotal();
            }
        });
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'editSaleModal' } }));
    }

    function calculateEditSaleTotal() {
        const quantity = parseFloat(document.getElementById('edit_sale_quantity').value) || 0;
        const price = parseFloat(document.getElementById('edit_sale_price_per_kg').value) || 0;
        const total = quantity * price;
        document.getElementById('edit_sale_total').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    // Funciones para el modal de crear venta
    function openCreateSaleModal() {
        const modal = document.getElementById('createSaleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateSaleModal() {
        const modal = document.getElementById('createSaleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
        
        // Limpiar el formulario
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            document.getElementById('create_sale_total').textContent = '$0';
        }
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            if (modalId === 'createSaleModal') {
                closeCreateSaleModal();
            }
        }
    }

    // Calcular total para el formulario de crear venta
    function calculateCreateSaleTotal() {
        const quantity = parseFloat(document.getElementById('create_sale_quantity').value) || 0;
        const price = parseFloat(document.getElementById('create_sale_price_per_kg').value) || 0;
        const total = quantity * price;
        document.getElementById('create_sale_total').textContent = '$' + total.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Para editar venta
        const quantityInput = document.getElementById('edit_sale_quantity');
        const priceInput = document.getElementById('edit_sale_price_per_kg');
        
        if (quantityInput) {
            quantityInput.addEventListener('input', calculateEditSaleTotal);
        }
        if (priceInput) {
            priceInput.addEventListener('input', calculateEditSaleTotal);
        }

        // Para crear venta
        const createQuantityInput = document.getElementById('create_sale_quantity');
        const createPriceInput = document.getElementById('create_sale_price_per_kg');
        const createCoffeeTypeSelect = document.getElementById('create_sale_coffee_type_id');
        
        if (createQuantityInput) {
            createQuantityInput.addEventListener('input', calculateCreateSaleTotal);
        }
        if (createPriceInput) {
            createPriceInput.addEventListener('input', calculateCreateSaleTotal);
        }
        if (createCoffeeTypeSelect) {
            createCoffeeTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                if (price) {
                    document.getElementById('create_sale_price_per_kg').value = price;
                    calculateCreateSaleTotal();
                }
            });
        }

        // Abrir modal automáticamente si hay errores o si se solicita
        @if(session('open_create_modal') || $errors->any())
            openCreateSaleModal();
        @endif
    });
</script>
@endpush
@endsection
