@extends('layouts.peasant')

@section('title', 'Mis Compras - Campesino')
@section('page-title', 'Mis Compras')

@section('content')
<div class="space-y-6">
    <!-- Tabla de compras -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Café</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio/kg</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($purchases as $purchase)
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
                                    <span class="font-medium text-gray-900">{{ $purchase->coffeeType->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ number_format($purchase->quantity, 2) }} kg</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${{ number_format($purchase->price_per_kg, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">${{ number_format($purchase->total, 0) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('peasant.purchases.show', $purchase) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
                                <p>No tienes compras registradas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

