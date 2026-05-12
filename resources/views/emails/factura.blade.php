<x-mail::message>
# Comprobante de Factura Electrónica

Estimado(a) **{{ $cliente }}**,

Le informamos que su pago ha sido procesado exitosamente en **Restaurante UDB**.

Adjunto a este correo encontrará su **Factura Electrónica de Consumidor Final** en formato PDF, la cual contiene todos los detalles fiscales de su consumo.

**Resumen de la Transacción:**
*   **Monto Total:** ${{ number_format($total, 2) }}
*   **Fecha:** {{ now()->format('d/m/Y H:i') }}
*   **Establecimiento:** Restaurante UDB - Sucursal Central

Si tiene alguna duda sobre su facturación, por favor responda a este correo o contáctenos al 1234-5678.

Gracias por su preferencia,

**Atentamente,**  
**Departamento de Facturación**  
**Restaurante UDB**
</x-mail::message>

