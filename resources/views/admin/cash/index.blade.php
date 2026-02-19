@extends('layouts.admin')

@section('title', 'Gestión de Caja - Administrador')
@section('page-title', 'Gestión de Caja')

@section('content')
<div class="space-y-6">
    <!-- Botón para abrir caja si no hay ninguna abierta -->
    @if(!$openCash)
        <div class="text-center">
            <button onclick="openModal('openCajaModal')" 
                    class="inline-flex items-center space-x-2 px-6 py-3 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors shadow-lg">
                <i class="fas fa-plus-circle"></i>
                <span class="font-semibold">Abrir Caja</span>
            </button>
        </div>
    @endif

    <!-- Información de caja abierta -->
    @if($openCash)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-coffee-900 flex items-center justify-center space-x-2">
                    <i class="fas fa-cash-register text-coffee-600"></i>
                    <span>Caja Actual: {{ $openCash->register_name }}</span>
                    <span class="ml-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Abierta</span>
                </h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Fecha de Apertura</p>
                    <p class="font-semibold text-gray-900 text-sm">
                        {{ $openCash->opening_date->format('d/m/Y') }}<br>
                        <span class="text-coffee-600">{{ $openCash->opening_date->format('H:i') }}</span>
                    </p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Saldo Disponible</p>
                    <p class="font-bold text-green-700 text-xl">${{ number_format($openCash->available_balance, 0) }}</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Kilos Comprados</p>
                    <p class="font-semibold text-blue-700 text-lg">{{ number_format($openCash->kilos_purchased, 2) }} kg</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-600 mb-1">Horas Abiertas</p>
                    <p class="font-semibold text-purple-700 text-lg" id="operating-time">
                        {{ $openCash->operating_time }}
                    </p>
                </div>
            </div>

            <!-- Botón para cerrar caja -->
            <div class="text-center">
                <button onclick="openModal('closeCajaModal')" 
                        class="inline-flex items-center space-x-2 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors shadow-lg">
                    <i class="fas fa-times-circle"></i>
                    <span class="font-semibold">Cerrar Caja</span>
                </button>
            </div>
        </div>
    @endif

    <!-- Historial de Cajas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history text-coffee-600 mr-2"></i>
                Historial de Cajas
            </h3>
        </div>
        <div class="p-6">
            @if($cashHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiempo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apertura</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cashHistory as $cash)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $cash->register_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ number_format($cash->kilos_purchased, 2) }} kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600 font-semibold">${{ number_format($cash->base_salary, 0) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600 font-semibold">${{ number_format($cash->available_balance, 0) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cash->status === 'open')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold" id="time-{{ $cash->id }}">
                                                {{ $cash->operating_time }}
                                            </span>
                                        @else
                                            <span class="text-gray-600 text-sm">
                                                {{ number_format($cash->operating_hours, 1) }}h
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>{{ $cash->opening_date->format('d/m/Y') }}</div>
                                        <div class="text-coffee-600 font-medium">{{ $cash->opening_date->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cash->status === 'open')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Abierto</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Cerrado</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="openModal('detailsModal{{ $cash->id }}')" 
                                                class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No hay registros de cajas.</p>
            @endif
        </div>
    </div>
</div>

<!-- Modal para abrir caja -->
<div id="openCajaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModalOnBackdrop(event, 'openCajaModal')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Abrir Nueva Caja</h3>
        </div>
        <form method="POST" action="{{ route('admin.cash.open') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="register_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Caja</label>
                    <input type="text" id="register_name" name="register_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                </div>
                <div>
                    <label for="initial_amount" class="block text-sm font-medium text-gray-700 mb-1">Cantidad Base (kg)</label>
                    <input type="number" id="initial_amount" name="initial_amount" step="0.01" min="0" value="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="opening_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" id="opening_date" name="opening_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    </div>
                    <div>
                        <label for="opening_time" class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                        <input type="time" id="opening_time" name="opening_time" value="{{ date('H:i') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                    </div>
                </div>
                <div>
                    <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-1">Monto Inicial ($)</label>
                    <input type="number" id="base_salary" name="base_salary" step="0.01" min="0" value="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-coffee-500 focus:border-coffee-500">
                </div>
            </div>
            <div class="mt-6 flex space-x-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors font-medium">
                    <i class="fas fa-save"></i> Abrir Caja
                </button>
                <button type="button" onclick="closeModal('openCajaModal')" 
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para cerrar caja -->
@if($openCash)
<div id="closeCajaModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModalOnBackdrop(event, 'closeCajaModal')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Cerrar Caja</h3>
        </div>
        <div class="p-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                <p class="text-yellow-800 font-semibold">
                    <i class="fas fa-exclamation-triangle"></i> ¿Estás seguro de cerrar la caja?
                </p>
            </div>
            <div class="space-y-2 mb-4">
                <p><strong>Caja:</strong> {{ $openCash->register_name }}</p>
                <p><strong>Fecha de Apertura:</strong><br>
                    <span class="text-gray-700">{{ $openCash->opening_date->format('d/m/Y') }}</span><br>
                    <span class="text-coffee-600 font-medium">{{ $openCash->opening_date->format('H:i') }}</span>
                </p>
                <p><strong>Tiempo operativo:</strong> <span id="close-time">{{ $openCash->operating_time }}</span></p>
                <p><strong>Saldo actual:</strong> ${{ number_format($openCash->available_balance, 0) }}</p>
            </div>
            <form method="POST" action="{{ route('admin.cash.close') }}">
                @csrf
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        <i class="fas fa-check"></i> Sí, cerrar caja
                    </button>
                    <button type="button" onclick="closeModal('closeCajaModal')" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modales de detalles -->
@foreach($cashHistory as $cash)
<div id="detailsModal{{ $cash->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="closeModalOnBackdrop(event, 'detailsModal{{ $cash->id }}')">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                <i class="fas fa-info-circle text-coffee-600 mr-2"></i>
                Detalles de la Caja: {{ $cash->register_name }}
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Información General</h4>
                    <div class="space-y-2 text-sm">
                        <p><strong>Cantidad Inicial:</strong> {{ number_format($cash->initial_amount, 2) }} kg</p>
                        <p><strong>Monto Inicial:</strong> ${{ number_format($cash->base_salary, 0) }}</p>
                        <p><strong>Monto Final:</strong> ${{ number_format($cash->available_balance, 0) }}</p>
                        <p><strong>Diferencia:</strong> 
                            <span class="{{ ($cash->available_balance - $cash->base_salary) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                ${{ number_format($cash->available_balance - $cash->base_salary, 0) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">Información Temporal</h4>
                    <div class="space-y-2 text-sm">
                        <p><strong>Fecha Apertura:</strong><br>
                            <span class="text-gray-700">{{ $cash->opening_date->format('d/m/Y') }}</span><br>
                            <span class="text-coffee-600 font-medium">{{ $cash->opening_date->format('H:i') }}</span>
                        </p>
                        <p><strong>Fecha Cierre:</strong> 
                            @if($cash->closing_date)
                                <br><span class="text-gray-700">{{ $cash->closing_date->format('d/m/Y') }}</span><br>
                                <span class="text-coffee-600 font-medium">{{ $cash->closing_date->format('H:i') }}</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">En curso</span>
                            @endif
                        </p>
                        <p><strong>Tiempo Operativo:</strong> 
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                @if($cash->status === 'open')
                                    {{ $cash->operating_time }}
                                @else
                                    {{ number_format($cash->operating_hours, 2) }}h
                                @endif
                            </span>
                        </p>
                        <p><strong>Estado:</strong> 
                            @if($cash->status === 'open')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Abierto</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Cerrado</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-3">Estadísticas de Operación</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p><strong>Kilos Comprados:</strong> {{ number_format($cash->kilos_purchased, 2) }} kg</p>
                    </div>
                    <div>
                        <p><strong>Kilos Vendidos:</strong> {{ number_format($cash->kilos_sold, 2) }} kg</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200">
            <button onclick="closeModal('detailsModal{{ $cash->id }}')" 
                    class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
    document.getElementById(modalId).classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function closeModalOnBackdrop(event, modalId) {
    if (event.target.id === modalId) {
        closeModal(modalId);
    }
}

// Actualizar tiempo en tiempo real para cajas abiertas
@if($openCash)
document.addEventListener('DOMContentLoaded', function() {
    const openingTime = new Date('{{ $openCash->opening_date->toIso8601String() }}').getTime();
    
    function updateTime() {
        const now = Date.now();
        const diff = now - openingTime;
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        const timeElement = document.getElementById('operating-time');
        const closeTimeElement = document.getElementById('close-time');
        
        if (timeElement) {
            timeElement.textContent = hours + 'h ' + minutes + 'm';
        }
        if (closeTimeElement) {
            closeTimeElement.textContent = hours + 'h ' + minutes + 'm';
        }
    }
    
    updateTime();
    setInterval(updateTime, 60000); // Actualizar cada minuto
});
@endif
</script>
@endpush
@endsection
