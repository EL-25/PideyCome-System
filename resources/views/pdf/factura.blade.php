<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Electrónica - Restaurante UDB</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #059669; padding-bottom: 20px; }
        .header h1 { color: #059669; margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 2px 0; color: #666; }
        
        .info-section { width: 100%; margin-bottom: 30px; }
        .info-box { width: 48%; display: inline-block; vertical-align: top; }
        .info-title { font-weight: bold; color: #059669; text-transform: uppercase; font-size: 10px; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
        
        .totals { width: 100%; text-align: right; }
        .totals-table { width: 250px; margin-left: auto; }
        .totals-table td { padding: 5px 10px; border: none; }
        .total-row { font-size: 16px; font-weight: bold; color: #059669; background-color: #ecfdf5; }
        
        .footer { margin-top: 50px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .stamp { border: 2px solid #059669; color: #059669; padding: 10px; display: inline-block; font-weight: bold; transform: rotate(-5deg); margin-top: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Restaurante UDB</h1>
        <p><strong>SUCURSAL CENTRAL - SAN SALVADOR</strong></p>
        <p>NIT: 0614-123456-101-1 | NRC: 123456-7</p>
        <p>Teléfono: 2200-0000 | Email: facturacion@restauranteudb.com</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <div class="info-title">Datos del Cliente</div>
            <p><strong>Nombre:</strong> {{ $cliente }}<br>
            <strong>Fecha de Emisión:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="info-box" style="text-align: right;">
            <div class="info-title">Comprobante Electrónico (DTE)</div>
            <p style="font-size: 9px; margin-bottom: 2px;"><strong>Código de Generación:</strong><br>
               {{ strtoupper(\Illuminate\Support\Str::uuid()) }}</p>
            <p style="font-size: 9px;"><strong>Sello de Recepción:</strong><br>
               {{ now()->format('Ymd') }}{{ strtoupper(\Illuminate\Support\Str::random(12)) }}</p>
            <p style="font-size: 11px; margin-top: 5px; color: #059669; font-weight: bold;">Consumidor Final</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Cant.</th>
                <th>Descripción del Producto</th>
                <th style="text-align: right; width: 15%;">Precio Unit.</th>
                <th style="text-align: right; width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
                @foreach($pedido->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>{{ $detalle->producto_nombre }}</td>
                        <td style="text-align: right;">${{ number_format($detalle->precio, 2) }}</td>
                        <td style="text-align: right;">${{ number_format($detalle->precio * $detalle->cantidad, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table class="totals-table">
            <tr class="total-row">
                <td>TOTAL A PAGAR:</td>
                <td>${{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Este documento es una representación gráfica de una Factura Electrónica de Consumidor Final.<br>
        SIguiendo el Reglamento del Código Tributario, autorizado por el Ministerio de Hacienda de El Salvador.</p>
        <div class="stamp">DOCUMENTO PROCESADO</div>
        <p style="margin-top: 20px;">Gracias por preferir Restaurante UDB</p>
    </div>
</body>
</html>
