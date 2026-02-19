@extends('layouts.admin')

@section('title', 'Bodega - Administrador')
@section('page-title', 'Gestión de Bodega')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Inventario de Bodega</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona el inventario de café</p>
        </div>
        <a href="{{ route('admin.warehouse.movements.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nuevo Movimiento</span>
        </a>
    </div>

    <!-- Tabla de inventario -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="inventoryTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Café</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comprado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponible</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inventory as $item)
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
                                    <span class="font-medium text-gray-900">{{ $item['coffee_type']->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($item['purchased'], 2) }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($item['sold'], 2) }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $item['available'] > 0 ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900' }}">
                                    {{ number_format($item['available'], 2) }} kg
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
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

    <!-- Movimientos Recientes: compras y ventas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-coffee-600 mr-2"></i>
                Movimientos recientes (entradas y salidas)
            </h3>
            <p class="text-sm text-gray-500 mt-1">Últimas compras y ventas</p>
        </div>
        <div class="p-6">
            @if($recent_movements->count() > 0)
                <div class="space-y-3">
                    @foreach($recent_movements as $movement)
                        <div class="flex items-center justify-between p-4 rounded-lg {{ $movement->type === 'compra' ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $movement->type === 'compra' ? 'bg-green-200 text-green-800' : 'bg-amber-200 text-amber-800' }}">
                                    <i class="fas {{ $movement->type === 'compra' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $movement->type === 'compra' ? 'bg-green-200 text-green-900' : 'bg-amber-200 text-amber-900' }}">
                                            {{ $movement->type === 'compra' ? 'Entrada (Compra)' : 'Salida (Venta)' }}
                                        </span>
                                        {{ $movement->coffee_type_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ number_format($movement->quantity, 2) }} kg
                                        <span class="text-gray-500">· {{ $movement->detail }}</span>
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 whitespace-nowrap">
                                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-center">
                    {{ $recent_movements->withQueryString()->links('vendor.pagination.tailwind-movimientos') }}
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay compras ni ventas recientes</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<style>
    #inventoryTable_wrapper .dataTables_length,
    #inventoryTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #inventoryTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #inventoryTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#inventoryTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#inventoryTable')) {
                $('#inventoryTable').DataTable().destroy();
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
            order: [[0, 'asc']], // Ordenar por tipo de café
            searching: false,
            paging: false,
            info: false,
            dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t>'
            });
        }
    });
</script>
@endpush
@endsection

