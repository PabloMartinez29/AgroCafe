@extends('layouts.admin')

@section('title', 'Tipos de Café - Administrador')
@section('page-title', 'Gestión de Tipos de Café')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Tipos de Café</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona los tipos y variedades de café</p>
        </div>
        <a href="{{ route('admin.coffee-types.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nuevo Tipo</span>
        </a>
    </div>

    <!-- Tabla de tipos de café -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="coffeeTypesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variedad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Procesamiento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Base</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($coffee_types as $type)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                                            <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                            <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $type->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-coffee-100 text-coffee-800">
                                    {{ ucfirst($type->variety) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $type->quality === 'premium' ? 'bg-coffee-200 text-coffee-900' : ($type->quality === 'special' ? 'bg-coffee-300 text-coffee-900' : 'bg-coffee-100 text-coffee-800') }}">
                                    {{ ucfirst($type->quality) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $type->processing_type_spanish }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">${{ number_format($type->base_price, 2, ',', '.') }} COP/kg</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $type->active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900' }}">
                                    {{ $type->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewCoffeeTypeModal('{{ $type->name }}', '{{ $type->variety }}', '{{ $type->quality }}', '{{ $type->processing_type }}', {{ $type->base_price }}, {{ $type->active ? 'true' : 'false' }}, '{{ $type->description ?? '' }}')"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditCoffeeTypeModal({{ $type->id }}, '{{ $type->name }}', '{{ $type->variety }}', '{{ $type->quality }}', '{{ $type->processing_type }}', {{ $type->base_price }}, {{ $type->active ? 'true' : 'false' }}, '{{ addslashes($type->description ?? '') }}')"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.coffee-types.destroy', $type) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este tipo de café?');">
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
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modales -->
@include('admin.coffee-types._modal-view')
@include('admin.coffee-types._modal-edit')

@push('scripts')
<style>
    #coffeeTypesTable_wrapper .dataTables_length,
    #coffeeTypesTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #coffeeTypesTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #coffeeTypesTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #coffeeTypesTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #coffeeTypesTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #coffeeTypesTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#coffeeTypesTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#coffeeTypesTable')) {
                $('#coffeeTypesTable').DataTable().destroy();
            }
            
            // Inicializar DataTables
            table.DataTable({
                processing: false,
                serverSide: false,
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
            responsive: true,
            autoWidth: false,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
            order: [[0, 'asc']], // Ordenar por nombre
            columnDefs: [
                { orderable: false, targets: 6 } // Deshabilitar ordenamiento en columna de acciones
            ],
            dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t><"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 space-y-2 sm:space-y-0"ip>'
            });
        }
    });

    function openViewCoffeeTypeModal(name, variety, quality, processingType, basePrice, active, description) {
        document.getElementById('view_coffee_type_name').textContent = name;
        document.getElementById('view_coffee_type_variety').textContent = variety.charAt(0).toUpperCase() + variety.slice(1);
        document.getElementById('view_coffee_type_variety_quality').textContent = variety.charAt(0).toUpperCase() + variety.slice(1) + ' - ' + quality.charAt(0).toUpperCase() + quality.slice(1);
        const processingTypeTranslations = {
            'normal': 'Normal',
            'wet': 'Mojado',
            'dry': 'Seco',
            'pasilla': 'Pasilla'
        };
        document.getElementById('view_coffee_type_processing_type').textContent = processingTypeTranslations[processingType.toLowerCase()] || processingType.charAt(0).toUpperCase() + processingType.slice(1);
        document.getElementById('view_coffee_type_base_price').textContent = '$' + parseFloat(basePrice).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' COP/kg';
        
        const qualityBadge = document.getElementById('view_coffee_type_quality_badge');
        qualityBadge.textContent = quality.charAt(0).toUpperCase() + quality.slice(1);
        qualityBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (quality === 'premium' ? 'bg-coffee-200 text-coffee-900' : 
             (quality === 'special' ? 'bg-coffee-300 text-coffee-900' : 'bg-coffee-100 text-coffee-800'));
        
        const statusBadge = document.getElementById('view_coffee_type_status_badge');
        statusBadge.textContent = active ? 'Activo' : 'Inactivo';
        statusBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900');
        
        if (description) {
            document.getElementById('view_coffee_type_description').textContent = description;
            document.getElementById('view_coffee_type_description_container').style.display = 'block';
        } else {
            document.getElementById('view_coffee_type_description_container').style.display = 'none';
        }
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewCoffeeTypeModal' } }));
    }

    function openEditCoffeeTypeModal(id, name, variety, quality, processingType, basePrice, active, description) {
        document.getElementById('editCoffeeTypeForm').action = '{{ route('admin.coffee-types.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_coffee_type_name').value = name;
        document.getElementById('edit_coffee_type_variety').value = variety;
        document.getElementById('edit_coffee_type_quality').value = quality;
        document.getElementById('edit_coffee_type_processing_type').value = processingType;
        document.getElementById('edit_coffee_type_base_price').value = basePrice;
        document.getElementById('edit_coffee_type_active').checked = active;
        document.getElementById('edit_coffee_type_description').value = description || '';
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'editCoffeeTypeModal' } }));
    }
</script>
@endpush
@endsection

