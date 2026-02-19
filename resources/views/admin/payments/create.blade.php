@extends('layouts.admin')

@section('title', 'Nuevo Pago - Administrador')
@section('page-title', 'Registrar Nuevo Pago')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-3xl">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header del formulario -->
            <div class="bg-gradient-to-r from-coffee-600 to-coffee-700 px-8 py-6">
                <h3 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-credit-card mr-3"></i>
                    Nuevo Pago
                </h3>
                <p class="text-coffee-100 mt-1">Completa el formulario para registrar un nuevo pago</p>
            </div>

            <!-- Contenido del formulario -->
            <form action="{{ route('admin.payments.store', ['type' => $type]) }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="space-y-6">
                    @if($type === 'peasants')
                        <x-select 
                            label="Compra" 
                            name="purchase_id" 
                            required 
                            :error="$errors->first('purchase_id')"
                        >
                            <option value="">Seleccione una compra</option>
                            @foreach(\App\Models\Purchase::with('peasant', 'coffeeType')->where('status', 'completed')->get() as $purchase)
                                <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                    {{ $purchase->peasant->name }} - {{ $purchase->coffeeType->name }} - ${{ number_format($purchase->total, 0) }}
                                </option>
                            @endforeach
                        </x-select>
                    @else
                        <x-select 
                            label="Venta" 
                            name="sale_id" 
                            required 
                            :error="$errors->first('sale_id')"
                        >
                            <option value="">Seleccione una venta</option>
                            @foreach(\App\Models\Sale::with('cooperative', 'coffeeType')->where('status', 'completed')->get() as $sale)
                                <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                    {{ $sale->cooperative->name ?? $sale->client_name }} - {{ $sale->coffeeType->name }} - ${{ number_format($sale->total, 0) }}
                                </option>
                            @endforeach
                        </x-select>
                    @endif

                    <x-input 
                        label="Monto" 
                        name="amount" 
                        type="number" 
                        step="0.01" 
                        min="0.01"
                        value="{{ old('amount') }}" 
                        required 
                        :error="$errors->first('amount')"
                    />

                    <x-select 
                        label="Método de Pago" 
                        name="payment_method" 
                        required 
                        :error="$errors->first('payment_method')"
                    >
                        <option value="">Seleccione un método</option>
                        <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transferencia</option>
                        <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Efectivo</option>
                        <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Cheque</option>
                    </x-select>

                    <x-input 
                        label="Referencia" 
                        name="reference" 
                        value="{{ old('reference') }}" 
                        :error="$errors->first('reference')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input 
                            label="Fecha de Pago" 
                            name="payment_date" 
                            type="date" 
                            value="{{ old('payment_date', date('Y-m-d')) }}" 
                            required 
                            :error="$errors->first('payment_date')"
                        />

                        <x-select 
                            label="Estado" 
                            name="status" 
                            required 
                            :error="$errors->first('status')"
                        >
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="completed" {{ old('status', 'completed') === 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="failed" {{ old('status') === 'failed' ? 'selected' : '' }}>Fallido</option>
                        </x-select>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-end space-x-4 pt-8 mt-8 border-t border-gray-200">
                    <a href="{{ route('admin.payments.index', ['type' => $type]) }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-coffee-600 text-white rounded-xl hover:bg-coffee-700 transition-all font-medium shadow-lg hover:shadow-xl flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
