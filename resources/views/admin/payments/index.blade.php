@extends('layouts.admin')

@section('title', 'Pagos - Administrador')
@section('page-title', 'Gestión de Pagos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Pagos</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona los pagos del sistema</p>
        </div>
        <a href="{{ route('admin.payments.create', ['type' => $type]) }}" 
           class="inline-flex items-center space-x-2 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Nuevo Pago</span>
        </a>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1">
        <div class="flex space-x-1">
            <a href="{{ route('admin.payments.index', ['type' => 'sales']) }}" 
               class="flex-1 px-4 py-2 text-center rounded-lg transition-colors {{ $type === 'sales' ? 'bg-coffee-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-handshake mr-2"></i>
                Pagos de Ventas
            </a>
            <a href="{{ route('admin.payments.index', ['type' => 'peasants']) }}" 
               class="flex-1 px-4 py-2 text-center rounded-lg transition-colors {{ $type === 'peasants' ? 'bg-coffee-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                <i class="fas fa-users mr-2"></i>
                Pagos a Campesinos
            </a>
        </div>
    </div>

    <!-- Tabla de pagos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="paymentsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if($type === 'peasants')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campesino</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compra</th>
                        @else
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venta</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            @if($type === 'peasants')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $payment->purchase->peasant->name ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $payment->purchase->coffeeType->name ?? '-' }}
                                </td>
                            @else
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">
                                        {{ $payment->sale->cooperative->name ?? $payment->sale->client_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $payment->sale->coffeeType->name ?? '-' }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">
                                ${{ number_format($payment->amount, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ ucfirst($payment->payment_method) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->reference ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $payment->status === 'completed' ? 'bg-coffee-100 text-coffee-800' : ($payment->status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                   onclick="openViewPaymentModal('{{ $type }}', '{{ $type === 'peasants' ? ($payment->purchase->peasant->name ?? '') : ($payment->sale->cooperative->name ?? $payment->sale->client_name ?? '') }}', {{ $payment->amount }}, '{{ $payment->payment_method }}', '{{ $payment->reference ?? '' }}', '{{ $payment->payment_date->format('d/m/Y') }}', '{{ $payment->status }}')"
                                   class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </button>
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

<!-- Modal -->
@include('admin.payments._modal-view')

@push('scripts')
<style>
    #paymentsTable_wrapper .dataTables_length,
    #paymentsTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #paymentsTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #paymentsTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #paymentsTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #paymentsTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #paymentsTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#paymentsTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#paymentsTable')) {
                $('#paymentsTable').DataTable().destroy();
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
            order: [[5, 'desc']], // Ordenar por fecha descendente
            columnDefs: [
                { orderable: false, targets: 7 } // Deshabilitar ordenamiento en columna de acciones
            ],
            dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t><"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 space-y-2 sm:space-y-0"ip>'
            });
        }
    });

    function openViewPaymentModal(type, client, amount, method, reference, date, status) {
        document.getElementById('view_payment_type').textContent = type === 'peasants' ? 'Pago a Campesino' : 'Pago de Venta';
        document.getElementById('view_payment_client').textContent = client;
        document.getElementById('view_payment_amount').textContent = '$' + parseFloat(amount).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_payment_method').textContent = method.charAt(0).toUpperCase() + method.slice(1);
        document.getElementById('view_payment_reference').textContent = reference || '-';
        document.getElementById('view_payment_date').textContent = date;
        
        const statusBadge = document.getElementById('view_payment_status_badge');
        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (status === 'completed' ? 'bg-coffee-100 text-coffee-800' : 
             (status === 'pending' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900'));
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewPaymentModal' } }));
    }
</script>
@endpush
@endsection

