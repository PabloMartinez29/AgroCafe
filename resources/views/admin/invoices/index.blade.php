@extends('layouts.admin')

@section('title', 'Facturas - Administrador')
@section('page-title', 'Gestión de Facturas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Lista de Facturas</h3>
            <p class="text-sm text-gray-600 mt-1">Gestiona las facturas del sistema</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-select name="transaction_type" label="Tipo de Transacción">
                <option value="">Todos los tipos</option>
                <option value="purchase" {{ request('transaction_type') === 'purchase' ? 'selected' : '' }}>Compra</option>
                <option value="sale" {{ request('transaction_type') === 'sale' ? 'selected' : '' }}>Venta</option>
            </x-select>

            <x-select name="payment_status" label="Estado de Pago">
                <option value="">Todos los estados</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Parcial</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Pagado</option>
            </x-select>

            <div class="flex items-end">
                <button type="submit" 
                        class="px-3 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors"
                        title="Filtrar">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de facturas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-4">
        <div class="overflow-x-auto">
            <table id="invoicesTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente/Proveedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Pago</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900">#{{ $invoice->invoice_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $invoice->transaction_type === 'purchase' ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-200 text-coffee-900' }}">
                                    {{ $invoice->transaction_type === 'purchase' ? 'Compra' : 'Venta' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($invoice->transaction_type === 'purchase')
                                    <span class="text-sm text-gray-900">{{ $invoice->purchase->peasant->name ?? '-' }}</span>
                                @else
                                    <span class="text-sm text-gray-900">{{ $invoice->sale->cooperative->name ?? $invoice->sale->client_name ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">
                                ${{ number_format($invoice->total, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $invoice->invoice_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $invoice->payment_status === 'paid' ? 'bg-coffee-100 text-coffee-800' : ($invoice->payment_status === 'partial' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900') }}">
                                    {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button 
                                       onclick="openViewInvoiceModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}', '{{ $invoice->transaction_type }}', '{{ $invoice->invoice_date->format('d/m/Y') }}', {{ $invoice->total }}, '{{ $invoice->payment_status }}', '{{ $invoice->transaction_type === 'purchase' ? ($invoice->purchase->peasant->name ?? '') : ($invoice->sale->cooperative->name ?? $invoice->sale->client_name ?? '') }}', '{{ $invoice->transaction_type === 'purchase' ? ($invoice->purchase->coffeeType->name ?? '') : ($invoice->sale->coffeeType->name ?? '') }}', {{ $invoice->transaction_type === 'purchase' ? ($invoice->purchase->quantity ?? 0) : ($invoice->sale->quantity ?? 0) }})"
                                       class="text-green-400 hover:text-green-600 transition-colors" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                       onclick="openEditInvoiceModal({{ $invoice->id }}, '{{ $invoice->invoice_number }}', '{{ $invoice->invoice_date->format('Y-m-d') }}', {{ $invoice->subtotal }}, {{ $invoice->taxes }}, {{ $invoice->total }}, '{{ $invoice->payment_status }}', '{{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}')"
                                       class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                       onclick="openSendEmailModal({{ $invoice->id }}, '{{ $invoice->transaction_type === 'purchase' ? ($invoice->purchase->peasant->email ?? '') : ($invoice->sale->cooperative->email ?? '') }}')"
                                       class="text-purple-400 hover:text-purple-600 transition-colors" title="Enviar por correo">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <a href="{{ route('admin.invoices.generate', $invoice) }}" 
                                       class="text-coffee-600 hover:text-coffee-900 transition-colors" title="Descargar PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
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
@include('admin.invoices._modal-view')

<!-- Modal para Editar Factura -->
<div id="editInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50 overflow-y-auto" onclick="closeModalOnBackdrop(event, 'editInvoiceModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 my-8 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    Editar Factura
                </h3>
                <button onclick="closeEditInvoiceModal()" class="text-white hover:text-coffee-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <form id="editInvoiceForm" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input 
                    label="Número de Factura" 
                    name="invoice_number" 
                    id="edit_invoice_number"
                    required 
                />

                <x-input 
                    label="Fecha de Factura" 
                    name="invoice_date" 
                    type="date"
                    id="edit_invoice_date"
                    required 
                />

                <x-input 
                    label="Subtotal" 
                    name="subtotal" 
                    type="number"
                    step="0.01"
                    min="0"
                    id="edit_invoice_subtotal"
                    required 
                />

                <x-input 
                    label="Impuestos" 
                    name="taxes" 
                    type="number"
                    step="0.01"
                    min="0"
                    id="edit_invoice_taxes"
                />

                <x-input 
                    label="Total" 
                    name="total" 
                    type="number"
                    step="0.01"
                    min="0"
                    id="edit_invoice_total"
                    required 
                />

                <x-select 
                    label="Estado de Pago" 
                    name="payment_status" 
                    id="edit_invoice_payment_status"
                    required 
                >
                    <option value="pending">Pendiente</option>
                    <option value="paid">Pagado</option>
                    <option value="overdue">Vencido</option>
                </x-select>

                <x-input 
                    label="Fecha de Vencimiento" 
                    name="due_date" 
                    type="date"
                    id="edit_invoice_due_date"
                />
            </div>

            <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                <button type="button" onclick="closeEditInvoiceModal()" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Enviar Factura por Correo -->
<div id="sendEmailInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden items-center justify-center z-50" onclick="closeModalOnBackdrop(event, 'sendEmailInvoiceModal')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-8 py-6">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-envelope mr-3"></i>
                    Enviar Factura por Correo
                </h3>
                <button onclick="closeSendEmailInvoiceModal()" class="text-white hover:text-purple-200 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>

        <form id="sendEmailInvoiceForm" method="POST" class="p-8">
            @csrf
            <div class="mb-6">
                <label for="send_email_address" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-purple-600"></i>
                    Dirección de Correo
                </label>
                <input type="email" 
                       id="send_email_address" 
                       name="email" 
                       required
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="correo@ejemplo.com">
            </div>

            <div class="bg-purple-50 border-l-4 border-purple-400 p-4 mb-6">
                <p class="text-sm text-purple-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    La factura será enviada al correo especificado con un PDF adjunto.
                </p>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <button type="button" onclick="closeSendEmailInvoiceModal()" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Enviar Correo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
    #invoicesTable_wrapper .dataTables_length,
    #invoicesTable_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    
    #invoicesTable_wrapper .dataTables_filter input {
        margin-left: 0.5rem;
        padding: 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    
    #invoicesTable_wrapper .dataTables_info {
        padding-top: 0.75rem;
        margin-right: 1rem;
    }
    
    #invoicesTable_wrapper .dataTables_paginate {
        padding-top: 0.75rem;
        display: flex;
        gap: 0.5rem;
    }
    
    #invoicesTable_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
    }
    
    #invoicesTable td {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function() {
        // Verificar que la tabla exista y tenga columnas
        var table = $('#invoicesTable');
        if (table.length && table.find('thead th').length > 0) {
            // Destruir tabla si ya existe
            if ($.fn.DataTable.isDataTable('#invoicesTable')) {
                $('#invoicesTable').DataTable().destroy();
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
            order: [[4, 'desc']], // Ordenar por fecha descendente
            columnDefs: [
                { orderable: false, targets: 6 } // Deshabilitar ordenamiento en columna de acciones
            ],
            dom: '<"flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-2 sm:space-y-0"lf><"overflow-x-auto"t><"flex flex-col sm:flex-row justify-between items-start sm:items-center mt-4 space-y-2 sm:space-y-0"ip>'
            });
        }
    });

    function openViewInvoiceModal(id, invoiceNumber, transactionType, date, total, paymentStatus, client, coffeeType, quantity) {
        document.getElementById('view_invoice_number').textContent = '#' + invoiceNumber;
        document.getElementById('view_invoice_type').textContent = transactionType === 'purchase' ? 'Compra' : 'Venta';
        document.getElementById('view_invoice_date').textContent = date;
        document.getElementById('view_invoice_total').textContent = '$' + parseFloat(total).toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        document.getElementById('view_invoice_client').textContent = client;
        document.getElementById('view_invoice_coffee_type').textContent = coffeeType;
        document.getElementById('view_invoice_quantity').textContent = parseFloat(quantity).toLocaleString('es-CO', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg';
        
        const typeBadge = document.getElementById('view_invoice_type_badge');
        typeBadge.textContent = transactionType === 'purchase' ? 'Compra' : 'Venta';
        typeBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (transactionType === 'purchase' ? 'bg-coffee-100 text-coffee-800' : 'bg-coffee-200 text-coffee-900');
        
        const statusBadge = document.getElementById('view_invoice_payment_status_badge');
        statusBadge.textContent = paymentStatus === 'paid' ? 'Pagado' : (paymentStatus === 'partial' ? 'Parcial' : 'Pendiente');
        statusBadge.className = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ' + 
            (paymentStatus === 'paid' ? 'bg-coffee-100 text-coffee-800' : 
             (paymentStatus === 'partial' ? 'bg-coffee-200 text-coffee-900' : 'bg-coffee-300 text-coffee-900'));
        
        // Configurar URL del PDF
        const pdfBtn = document.getElementById('generatePdfBtn');
        pdfBtn.dataset.url = '{{ route('admin.invoices.generate', ':id') }}'.replace(':id', id);
        
        window.dispatchEvent(new CustomEvent('open-modal', { detail: { id: 'viewInvoiceModal' } }));
    }

    function openEditInvoiceModal(id, invoiceNumber, invoiceDate, subtotal, taxes, total, paymentStatus, dueDate) {
        document.getElementById('editInvoiceForm').action = '{{ route('admin.invoices.update', ':id') }}'.replace(':id', id);
        document.getElementById('edit_invoice_number').value = invoiceNumber;
        document.getElementById('edit_invoice_date').value = invoiceDate;
        document.getElementById('edit_invoice_subtotal').value = subtotal;
        document.getElementById('edit_invoice_taxes').value = taxes || 0;
        document.getElementById('edit_invoice_total').value = total;
        document.getElementById('edit_invoice_payment_status').value = paymentStatus;
        document.getElementById('edit_invoice_due_date').value = dueDate || '';
        
        openEditInvoiceModalDisplay();
    }

    function openEditInvoiceModalDisplay() {
        const modal = document.getElementById('editInvoiceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeEditInvoiceModal() {
        const modal = document.getElementById('editInvoiceModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function openSendEmailModal(invoiceId, defaultEmail) {
        document.getElementById('sendEmailInvoiceForm').action = '{{ route('admin.invoices.send-email', ':id') }}'.replace(':id', invoiceId);
        document.getElementById('send_email_address').value = defaultEmail || '';
        
        const modal = document.getElementById('sendEmailInvoiceModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeSendEmailInvoiceModal() {
        const modal = document.getElementById('sendEmailInvoiceModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function closeModalOnBackdrop(event, modalId) {
        if (event.target.id === modalId) {
            if (modalId === 'editInvoiceModal') {
                closeEditInvoiceModal();
            } else if (modalId === 'sendEmailInvoiceModal') {
                closeSendEmailInvoiceModal();
            }
        }
    }
</script>
@endpush
@endsection

