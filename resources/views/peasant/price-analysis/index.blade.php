@extends('layouts.peasant')

@section('title', 'Análisis de Precios - Campesino')
@section('page-title', 'Análisis de Precios del Café')

@section('content')
<div class="space-y-6">
    <!-- Información General -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-line text-coffee-600 mr-2"></i>
                    Análisis de Precios en Tiempo Real
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Monitorea la evolución de los precios de todas las variedades de café.
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500" id="lastUpdate">Actualizando...</span>
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" id="statusIndicator"></div>
            </div>
        </div>
    </div>

    @if(count($coffee_types_data) > 0)
        <!-- Tarjetas de Precios Actuales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="priceCardsContainer">
            @foreach($coffee_types_data as $type_data)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 price-card" data-coffee-type-id="{{ $type_data['id'] }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $type_data['name'] }}</p>
                            <p class="text-xs text-gray-500">
                                {{ \App\Models\CoffeeType::translateProcessingType($type_data['processing_type']) }}
                                - {{ ucfirst($type_data['variety']) }}
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-coffee-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <ellipse cx="12" cy="12" rx="6" ry="9" fill="#7a5f47"/>
                                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#5a4535" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                <ellipse cx="12" cy="14" rx="4" ry="5" fill="#5a4535" opacity="0.3"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-2xl font-bold text-gray-900 price-current" data-price="{{ $type_data['current_price'] }}">
                            ${{ number_format($type_data['current_price'], 2, ',', '.') }} COP
                        </p>
                        @if($type_data['price_change'] !== null)
                            <div class="flex items-center mt-1">
                                <span class="text-xs price-change {{ $type_data['price_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}" data-change="{{ $type_data['price_change'] }}">
                                    {{ $type_data['price_change'] >= 0 ? '+' : '' }}${{ number_format($type_data['price_change'], 2, ',', '.') }} COP
                                </span>
                                @if($type_data['price_change_percent'] !== null)
                                    <span class="text-xs ml-2 price-change-percent {{ $type_data['price_change_percent'] >= 0 ? 'text-green-600' : 'text-red-600' }}" data-change-percent="{{ $type_data['price_change_percent'] }}">
                                        ({{ $type_data['price_change_percent'] >= 0 ? '+' : '' }}{{ number_format($type_data['price_change_percent'], 2) }}%)
                                    </span>
                                @endif
                                <i class="fas price-change-icon {{ $type_data['price_change'] >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs ml-1 {{ $type_data['price_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Gráfica en Tiempo Real - Todas las líneas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-chart-area text-coffee-600 mr-2"></i>
                        Gráfica Comparativa de Precios
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Últimos 30 días - Todas las variedades de café</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Precio Mínimo Global</p>
                        <p class="text-lg font-bold text-gray-900" id="minPrice">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Precio Máximo Global</p>
                        <p class="text-lg font-bold text-gray-900" id="maxPrice">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Precio Promedio Global</p>
                        <p class="text-lg font-bold text-gray-900" id="avgPrice">-</p>
                    </div>
                </div>
            </div>
            <div class="h-96">
                <canvas id="realtimeChart"></canvas>
            </div>
        </div>

        <!-- Historial de Precios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-coffee-600 mr-2"></i>
                Historial de Precios (Últimos 30 días)
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Café</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Procesamiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($historical_prices_list as $price)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($price['date'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $price['coffee_type'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \App\Models\CoffeeType::translateProcessingType($price['processing_type']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-coffee-600">
                                    ${{ number_format($price['price'], 2, ',', '.') }} COP
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-chart-line text-4xl mb-4 text-gray-300"></i>
                                    <p>No hay datos históricos disponibles. Los precios mostrados son simulados basados en los precios base.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mb-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="12" cy="12" rx="6" ry="9" fill="currentColor"/>
                <path d="M12 4 Q10 8 10 12 Q10 16 12 20" stroke="#9ca3af" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                <ellipse cx="12" cy="14" rx="4" ry="5" fill="#9ca3af" opacity="0.3"/>
            </svg>
            <p class="text-gray-500">No hay tipos de café disponibles para analizar.</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    @if(count($coffee_types_data) > 0)
        @php
            $colors = [
                ['border' => '#dc2626', 'bg' => 'rgba(220, 38, 38, 0.1)'],
                ['border' => '#7a5f47', 'bg' => 'rgba(122, 95, 71, 0.1)'],
                ['border' => '#16a34a', 'bg' => 'rgba(22, 163, 74, 0.1)'],
                ['border' => '#ea580c', 'bg' => 'rgba(234, 88, 12, 0.1)'],
                ['border' => '#6366f1', 'bg' => 'rgba(99, 102, 241, 0.1)'],
                ['border' => '#ec4899', 'bg' => 'rgba(236, 72, 153, 0.1)'],
                ['border' => '#14b8a6', 'bg' => 'rgba(20, 184, 166, 0.1)'],
                ['border' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.1)'],
            ];

            $initialDatasetsData = [];
            foreach($coffee_types_data as $index => $type_data) {
                $color = $colors[$index % count($colors)];
                $prices_array = [];
                $last_price = $type_data['current_price'];

                foreach(array_values($all_dates) as $date) {
                    if (isset($type_data['prices_by_date'][$date])) {
                        $last_price = $type_data['prices_by_date'][$date];
                        $prices_array[] = $last_price;
                    } else {
                        $prices_array[] = $last_price;
                    }
                }

                $processingTypeLabel = \App\Models\CoffeeType::translateProcessingType($type_data['processing_type']);
                $labelText = $type_data['name'] . ' (' . $processingTypeLabel . ')';
                $initialDatasetsData[] = [
                    'label' => $labelText,
                    'data' => $prices_array,
                    'borderColor' => $color['border'],
                    'backgroundColor' => $color['bg'],
                    'borderWidth' => 2,
                    'fill' => false,
                    'tension' => 0.4,
                    'pointRadius' => 2,
                    'pointHoverRadius' => 4,
                    'pointBackgroundColor' => $color['border'],
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 1,
                    'coffee_type_id' => $type_data['id'],
                    'current_price' => $type_data['current_price'],
                ];
            }
        @endphp

        let realtimeChart = null;

        function initChart() {
            if (typeof Chart === 'undefined') {
                setTimeout(initChart, 100);
                return;
            }

            const initialDatasets = @json($initialDatasetsData ?? []);
            const initialLabels = @json($chart_labels ?? []);

            if (!initialLabels || initialLabels.length === 0 || !initialDatasets || initialDatasets.length === 0) {
                return;
            }

            const ctx = document.getElementById('realtimeChart');
            if (!ctx) {
                return;
            }

            if (realtimeChart) {
                realtimeChart.destroy();
            }

            realtimeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: initialLabels,
                    datasets: initialDatasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: {
                                    size: 11,
                                },
                            },
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    const value = parseFloat(context.parsed.y);
                                    const formatted = value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    return context.dataset.label + ': $' + formatted + ' COP';
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    const formatted = parseFloat(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    return '$' + formatted + ' COP';
                                },
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                        },
                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false,
                    },
                },
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initChart);
        } else {
            initChart();
        }
    @endif
</script>
@endpush
@endsection


