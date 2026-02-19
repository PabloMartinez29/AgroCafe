@extends('layouts.admin')

@section('title', 'Usuarios - Administrador')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="space-y-6">
    <!-- Header con botón de crear -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Usuarios</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona los usuarios del sistema</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nuevo Usuario</span>
        </a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="usersTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-coffee-600"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-100 text-coffee-800' }}">
                                    {{ $user->role === 'admin' ? 'Administrador' : 'Campesino' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $user->active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900' }}">
                                    {{ $user->active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone ?? '' }}', '{{ $user->address ?? '' }}', '{{ $user->role }}', {{ $user->active ? 'true' : 'false' }}, '{{ $user->created_at->format('d/m/Y') }}')"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->phone ?? '' }}', '{{ $user->address ?? '' }}', '{{ $user->role }}', {{ $user->active ? 'true' : 'false' }})"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!$user->active)
                                        <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-400 hover:text-green-600 transition-colors" 
                                                    title="Activar">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $user) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de desactivar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-600 transition-colors" 
                                                title="Desactivar">
                                            <i class="fas fa-ban"></i>
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
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modales -->
@include('admin.users._modal-view')
@include('admin.users._modal-edit')

@push('scripts')
<style>
    /* Estilos personalizados para DataTables */
    #usersTable_wrapper .dataTables_length,
    #usersTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #usersTable_wrapper .dataTables_length label,
    #usersTable_wrapper .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    #usersTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #usersTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #usersTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #usersTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    /* Espaciado para fechas y números */
    #usersTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#usersTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable().destroy();
            }
            
            // Inicializar DataTables
            table.DataTable({
                processing: false,
                serverSide: false,
                responsive: true,
                autoWidth: false,
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
                order: [[0, 'asc']], // Ordenar por nombre
                columnDefs: [
                    { orderable: false, targets: 5 } // Deshabilitar ordenamiento en columna de acciones
                ],
                dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t><"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 space-y-2 sm:space-y-0"ip>',
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
                }
            });
        }
    });

    function openViewModal(id, name, email, phone, address, role, active, createdAt) {
        document.getElementById('view_name').textContent = name;
        document.getElementById('view_email').textContent = email;
        document.getElementById('view_phone').textContent = phone || '-';
        document.getElementById('view_address').textContent = address || '-';
        document.getElementById('view_created_at').textContent = createdAt;
        
        const roleBadge = document.getElementById('view_role_badge');
        roleBadge.textContent = role === 'admin' ? 'Administrador' : 'Campesino';
        roleBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (role === 'admin' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-100 text-coffee-800');
        
        const statusBadge = document.getElementById('view_status_badge');
        statusBadge.textContent = active ? 'Activo' : 'Inactivo';
        statusBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (active ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-300 text-coffee-900');
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewUserModal' } }));
    }

    function openEditModal(id, name, email, phone, address, role, active) {
        document.getElementById('editUserForm').action = '{{ route('admin.users.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_phone').value = phone || '';
        document.getElementById('edit_address').value = address || '';
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_active').checked = active;
        document.getElementById('edit_password').value = '';
        document.getElementById('edit_password_confirmation').value = '';
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'editUserModal' } }));
    }
</script>
@endpush
@endsection

