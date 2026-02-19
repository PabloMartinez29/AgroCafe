@extends('layouts.admin')

@section('title', 'Dashboard - Administrador')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-green-600 mt-1">
                        <i class="fas fa-check-circle"></i> {{ $stats['active_users'] }} activos
                    </p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Compras Completadas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_purchases'] }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ventas Completadas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_sales'] }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-handshake text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tipos de Café</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_coffee_types'] }}</p>
                </div>
                <div class="w-14 h-14 bg-coffee-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Grano de café - forma ovalada -->
                        <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                        <!-- Línea central característica del grano -->
                        <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        <!-- Sombra para dar profundidad -->
                        <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Compras y Ventas Recientes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Compras Recientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-shopping-cart text-coffee-600 mr-2"></i>
                    Compras Recientes
                </h3>
            </div>
            <div class="p-6">
                @if($recent_purchases->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_purchases as $purchase)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $purchase->peasant->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $purchase->coffeeType->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar"></i> {{ $purchase->purchase_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-coffee-600">{{ number_format($purchase->quantity, 2) }} kg</p>
                                    <p class="text-sm text-gray-600">${{ number_format($purchase->total, 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No hay compras recientes</p>
                @endif
            </div>
        </div>

        <!-- Ventas Recientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-handshake text-purple-600 mr-2"></i>
                    Ventas Recientes
                </h3>
            </div>
            <div class="p-6">
                @if($recent_sales->count() > 0)
                    <div class="space-y-4">
                        @foreach($recent_sales as $sale)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">
                                        {{ $sale->cooperative ? $sale->cooperative->name : $sale->client_name }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $sale->coffeeType->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-calendar"></i> {{ $sale->sale_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-purple-600">{{ number_format($sale->quantity, 2) }} kg</p>
                                    <p class="text-sm text-gray-600">${{ number_format($sale->total, 0) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No hay ventas recientes</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráficas de Ventas y Compras -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfica de Compras -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-shopping-cart text-green-600 mr-2"></i>
                    Compras de Café
                </h3>
                <span class="text-xs text-gray-500">Últimos 6 meses</span>
            </div>
            <div class="h-64">
                <canvas id="purchasesChart"></canvas>
            </div>
        </div>

        <!-- Gráfica de Ventas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-handshake text-coffee-600 mr-2"></i>
                    Ventas de Café
                </h3>
                <span class="text-xs text-gray-500">Últimos 6 meses</span>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Datos para las gráficas
    const months = @json($months);
    const purchasesData = @json($purchases_data);
    const salesData = @json($sales_data);

    // Configuración común para las gráficas
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return '$' + context.parsed.y.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                    },
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    }
                },
                grid: {
                    display: false
                }
            }
        }
    };

    // Gráfica de Compras
    const purchasesCtx = document.getElementById('purchasesChart').getContext('2d');
    new Chart(purchasesCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Compras',
                data: purchasesData,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: 'rgb(34, 197, 94)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: chartOptions
    });

    // Gráfica de Ventas
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Ventas',
                data: salesData,
                borderColor: 'rgb(122, 95, 71)',
                backgroundColor: 'rgba(122, 95, 71, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: 'rgb(122, 95, 71)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: chartOptions
    });
</script>
@endpush
@endsection

