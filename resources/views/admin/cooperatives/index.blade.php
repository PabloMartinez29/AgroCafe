@extends('layouts.admin')

@section('title', 'Cooperativas - Administrador')
@section('page-title', 'Gestión de Cooperativas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Cooperativas</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona las cooperativas del sistema</p>
        </div>
        <a href="{{ route('admin.cooperatives.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nueva Cooperativa</span>
        </a>
    </div>

    <!-- Tabla de cooperativas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="cooperativesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Representante Legal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cooperatives as $cooperative)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-building text-coffee-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $cooperative->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cooperative->nit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cooperative->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cooperative->email ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $cooperative->legal_representative ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $cooperative->active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900' }}">
                                    {{ $cooperative->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewCooperativeModal('{{ $cooperative->name }}', '{{ $cooperative->nit }}', '{{ $cooperative->phone ?? '' }}', '{{ $cooperative->email ?? '' }}', '{{ $cooperative->address ?? '' }}', '{{ $cooperative->legal_representative ?? '' }}', {{ $cooperative->active ? 'true' : 'false' }}, '{{ $cooperative->created_at->format('d/m/Y') }}')"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditCooperativeModal({{ $cooperative->id }}, '{{ $cooperative->name }}', '{{ $cooperative->nit }}', '{{ $cooperative->phone ?? '' }}', '{{ $cooperative->email ?? '' }}', '{{ $cooperative->address ?? '' }}', '{{ $cooperative->legal_representative ?? '' }}', {{ $cooperative->active ? 'true' : 'false' }})"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.cooperatives.destroy', $cooperative) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta cooperativa?');">
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
@include('admin.cooperatives._modal-view')
@include('admin.cooperatives._modal-edit')

@push('scripts')
<style>
    #cooperativesTable_wrapper .dataTables_length,
    #cooperativesTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #cooperativesTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #cooperativesTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #cooperativesTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #cooperativesTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #cooperativesTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#cooperativesTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#cooperativesTable')) {
                $('#cooperativesTable').DataTable().destroy();
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

    function openViewCooperativeModal(name, nit, phone, email, address, legalRepresentative, active, createdAt) {
        document.getElementById('view_cooperative_name').textContent = name;
        document.getElementById('view_cooperative_nit').textContent = nit;
        document.getElementById('view_cooperative_phone').textContent = phone || '-';
        document.getElementById('view_cooperative_email').textContent = email || '-';
        document.getElementById('view_cooperative_address').textContent = address || '-';
        document.getElementById('view_cooperative_legal_representative').textContent = legalRepresentative || '-';
        document.getElementById('view_cooperative_created_at').textContent = createdAt;
        
        const statusBadge = document.getElementById('view_cooperative_status_badge');
        statusBadge.textContent = active ? 'Activo' : 'Inactivo';
        statusBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900');
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewCooperativeModal' } }));
    }

    function openEditCooperativeModal(id, name, nit, phone, email, address, legalRepresentative, active) {
        document.getElementById('editCooperativeForm').action = '{{ route('admin.cooperatives.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_cooperative_name').value = name;
        document.getElementById('edit_cooperative_nit').value = nit;
        document.getElementById('edit_cooperative_phone').value = phone || '';
        document.getElementById('edit_cooperative_email').value = email || '';
        document.getElementById('edit_cooperative_address').value = address || '';
        document.getElementById('edit_cooperative_legal_representative').value = legalRepresentative || '';
        document.getElementById('edit_cooperative_active').checked = active;
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'editCooperativeModal' } }));
    }
</script>
@endpush
@endsection

