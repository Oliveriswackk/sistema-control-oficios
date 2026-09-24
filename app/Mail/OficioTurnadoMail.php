<?php

namespace App\Mail;

use App\Models\NotificacionTurnado;
use App\Models\Turnado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;

class OficioTurnadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Turnado $turnado;

    public bool $esResponsable;

    public NotificacionTurnado $notificacion;

    public function __construct(
        Turnado $turnado,
        NotificacionTurnado $notificacion
    ) {
        $this->turnado = $turnado;
        $this->notificacion = $notificacion;
        $this->esResponsable = (int) $turnado->tipo_participacion_id === 1;
    }

    public function build()
    {
        $turnado = $this->turnado;
        $oficio = $turnado->oficio;

        $archivo = $oficio->archivos
            ->flatMap(function ($archivo) {
                return $archivo->versiones;
            })
            ->where('es_actual', true)
            ->first();

        $this->withSymfonyMessage(function (Email $message) {
            $message->getHeaders()->addIdHeader(
                'Message-ID',
                $this->notificacion->message_id
            );
        });

        $mail = $this
            ->subject(
                '[Oficio turnado] ' .
                $oficio->numero_oficio .
                ' — ' .
                $oficio->asunto
            )
            ->view('emails.oficio-turnado');

        if ($archivo && Storage::disk('public')->exists($archivo->ruta)) {
            $mail->attach(
                Storage::disk('public')->path($archivo->ruta),
                [
                    'as' => $oficio->numero_oficio . '.pdf',
                    'mime' => 'application/pdf',
                ]
            );
        }

        return $mail;
    }
}