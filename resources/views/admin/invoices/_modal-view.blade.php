<!-- Modal para Ver Factura -->
<x-modal id="viewInvoiceModal" title="Detalles de la Factura" size="lg">
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div>
                <h4 class="text-2xl font-bold text-gray-900" id="view_invoice_number"></h4>
                <p class="text-sm text-gray-600 mt-1">Fecha: <span id="view_invoice_date"></span></p>
            </div>
            <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full" id="view_invoice_type_badge"></span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Transacción</p>
                <p class="text-lg font-semibold text-gray-900" id="view_invoice_type"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cliente/Proveedor</p>
                <p class="text-lg font-semibold text-gray-900" id="view_invoice_client"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Tipo de Café</p>
                <p class="text-lg font-semibold text-gray-900" id="view_invoice_coffee_type"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Cantidad</p>
                <p class="text-lg font-semibold text-gray-900" id="view_invoice_quantity"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total</p>
                <p class="text-2xl font-bold text-coffee-600" id="view_invoice_total"></p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Estado de Pago</p>
                <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full" id="view_invoice_payment_status_badge"></span>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
            <button type="button" 
                    @click="$dispatch('close-modal', { id: 'viewInvoiceModal' }); document.body.style.overflow = ''"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Cerrar
            </button>
            <button type="button"
                    id="generatePdfBtn"
                    onclick="var iframe = document.createElement('iframe'); iframe.style.display = 'none'; iframe.src = this.dataset.url; document.body.appendChild(iframe); setTimeout(function() { document.body.removeChild(iframe); }, 3000);"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Descargar PDF
            </button>
        </div>
    </div>
</x-modal>

