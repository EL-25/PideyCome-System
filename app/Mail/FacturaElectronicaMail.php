<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaElectronicaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedidos;
    public $total;
    public $cliente;

    /**
     * Create a new message instance.
     */
    public function __construct($pedidos, $total, $cliente)
    {
        $this->pedidos = $pedidos;
        $this->total = $total;
        $this->cliente = $cliente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura Electrónica - Restaurante UDB',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.factura',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.factura', [
            'pedidos' => $this->pedidos,
            'total'   => $this->total,
            'cliente' => $this->cliente
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'Factura_Electronica_UDB.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
