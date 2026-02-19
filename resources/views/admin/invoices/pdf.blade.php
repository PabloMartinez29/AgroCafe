<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #8b6b4f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #8b6b4f;
            margin: 0;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f5f0e8;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #8b6b4f;
            font-size: 14px;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #8b6b4f;
            color: white;
        }
        .total {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            color: #8b6b4f;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AgroCafé</h1>
        <h2>Factura #{{ $invoice->invoice_number }}</h2>
        <p>Fecha: {{ $invoice->invoice_date->format('d/m/Y') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>{{ $invoice->transaction_type === 'purchase' ? 'Campesino' : 'Cliente' }}</h3>
            <p>
                @if($invoice->transaction_type === 'purchase')
                    {{ $invoice->purchase->peasant->name ?? '-' }}<br>
                    {{ $invoice->purchase->peasant->email ?? '' }}<br>
                    {{ $invoice->purchase->peasant->phone ?? '' }}
                @else
                    {{ $invoice->sale->cooperative->name ?? $invoice->sale->client_name ?? '-' }}<br>
                    {{ $invoice->sale->cooperative->email ?? '' }}<br>
                    {{ $invoice->sale->cooperative->phone ?? '' }}
                @endif
            </p>
        </div>
        <div class="info-box">
            <h3>Información de la Factura</h3>
            <p>
                Tipo: {{ $invoice->transaction_type === 'purchase' ? 'Compra' : 'Venta' }}<br>
                Estado: {{ $invoice->payment_status === 'paid' ? 'Pagado' : ($invoice->payment_status === 'partial' ? 'Parcial' : 'Pendiente') }}
            </p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($invoice->transaction_type === 'purchase')
                        {{ $invoice->purchase->coffeeType->name ?? '-' }}
                    @else
                        {{ $invoice->sale->coffeeType->name ?? '-' }}
                    @endif
                </td>
                <td>
                    @if($invoice->transaction_type === 'purchase')
                        {{ number_format($invoice->purchase->quantity ?? 0, 2) }} kg
                    @else
                        {{ number_format($invoice->sale->quantity ?? 0, 2) }} kg
                    @endif
                </td>
                <td>
                    @if($invoice->transaction_type === 'purchase')
                        ${{ number_format($invoice->purchase->price_per_kg ?? 0, 0) }}/kg
                    @else
                        ${{ number_format($invoice->sale->price_per_kg ?? 0, 0) }}/kg
                    @endif
                </td>
                <td>${{ number_format($invoice->total, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Total: ${{ number_format($invoice->total, 0) }}
    </div>

    <div class="footer">
        <p>AgroCafé - Sistema de Gestión de Café</p>
        <p>Esta es una factura generada electrónicamente</p>
    </div>
</body>
</html>

