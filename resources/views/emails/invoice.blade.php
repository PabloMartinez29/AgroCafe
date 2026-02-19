<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header { 
            background: {{ $invoice->transaction_type === 'sale' ? '#8B4513' : '#228B22' }}; 
            color: white; 
            padding: 20px; 
            text-align: center; 
            border-radius: 10px 10px 0 0; 
        }
        .content { 
            padding: 30px; 
            background: #f9f9f9; 
        }
        .footer { 
            background: #f8f9fa; 
            padding: 15px; 
            text-align: center; 
            font-size: 12px; 
            color: #666; 
            border-radius: 0 0 10px 10px; 
        }
        .highlight { 
            background: #fff3cd; 
            padding: 15px; 
            border-left: 4px solid {{ $invoice->transaction_type === 'sale' ? '#8B4513' : '#228B22' }}; 
            margin: 15px 0; 
            border-radius: 5px; 
        }
        .tipo-badge { 
            background: {{ $invoice->transaction_type === 'sale' ? '#8B4513' : '#228B22' }}; 
            color: white; 
            padding: 5px 10px; 
            border-radius: 15px; 
            font-size: 12px; 
            font-weight: bold; 
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌱 AgroCafé</h1>
            <p>Factura Electrónica - {{ $invoice->transaction_type === 'sale' ? 'Cooperativa' : 'Campesino' }}</p>
        </div>
        
        <div class="content">
            <h2>Estimado/a {{ $clientName }},</h2>
            
            <p>Nos complace enviarle la factura correspondiente a su {{ $invoice->transaction_type === 'sale' ? 'venta' : 'compra' }} de café:</p>
            
            <div class="highlight">
                <h3 style="margin-top: 0; color: {{ $invoice->transaction_type === 'sale' ? '#8B4513' : '#228B22' }};">📄 Detalles de la Factura</h3>
                <p><strong>Número:</strong> {{ $invoice->invoice_number }} <span class="tipo-badge">{{ $invoice->transaction_type === 'sale' ? 'Cooperativa' : 'Campesino' }}</span></p>
                <p><strong>Fecha:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                @if($invoice->transaction_type === 'purchase' && $invoice->purchase)
                    <p><strong>Producto:</strong> {{ $invoice->purchase->coffeeType->name ?? 'N/A' }}</p>
                    <p><strong>Cantidad:</strong> {{ number_format($invoice->purchase->quantity, 2, ',', '.') }} kg</p>
                @elseif($invoice->transaction_type === 'sale' && $invoice->sale)
                    <p><strong>Producto:</strong> {{ $invoice->sale->coffeeType->name ?? 'N/A' }}</p>
                    <p><strong>Cantidad:</strong> {{ number_format($invoice->sale->quantity, 2, ',', '.') }} kg</p>
                @endif
                <p><strong>Total:</strong> <span style="font-size: 18px; color: {{ $invoice->transaction_type === 'sale' ? '#8B4513' : '#228B22' }};"><strong>${{ number_format($invoice->total, 0, ',', '.') }}</strong></span></p>
            </div>
            
            @if($invoice->transaction_type === 'purchase')
                <p><strong>🌾 Nota para Campesinos:</strong> Esta factura corresponde a la compra de su café. Gracias por ser parte de nuestra red de productores y por contribuir con café de calidad.</p>
            @else
                <p><strong>🏢 Nota para Cooperativas:</strong> Esta factura corresponde a su compra de café premium. Gracias por confiar en AgroCafé para sus necesidades de café.</p>
            @endif
            
            <p>📎 <strong>Adjunto encontrará la factura en formato PDF</strong> para sus registros contables y fiscales.</p>
            
            <p>Si tiene alguna pregunta sobre esta factura o necesita información adicional, no dude en contactarnos.</p>
            
            <p>Gracias por confiar en AgroCafé.</p>
            
            <p>Cordialmente,<br><strong>Equipo AgroCafé</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>🌱 AgroCafé - Conectando el Campo con el Mundo</strong></p>
            <p>📧 agrocafe1129@gmail.com | 📱 +57 350 888 4148</p>
            <p>🌐 www.agrocafe.com</p>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 10px 0;">
            <p><small>Este es un mensaje automático, por favor no responda directamente a este correo.</small></p>
        </div>
    </div>
</body>
</html>

