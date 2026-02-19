@extends('layouts.peasant')

@section('title', 'Dashboard - Campesino')
@section('page-title', 'Mi Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Compras</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_purchases'] }}</p>
                </div>
                <div class="w-14 h-14 bg-coffee-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-coffee-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completadas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['completed_purchases'] }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_purchases'] }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Compras Recientes -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-history text-coffee-600 mr-2"></i>
                    Mis Compras Recientes
                </h3>
                <a href="{{ route('peasant.purchases.index') }}" 
                   class="text-sm text-coffee-600 hover:text-coffee-700 font-medium">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            @if($recent_purchases->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_purchases as $purchase)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $purchase->coffeeType->name }}</p>
                                <p class="text-sm text-gray-600">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $purchase->status === 'completed' ? 'bg-coffee-100 text-coffee-800' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </p>
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
                <p class="text-gray-500 text-center py-8">No tienes compras registradas</p>
            @endif
        </div>
    </div>
</div>
@endsection

