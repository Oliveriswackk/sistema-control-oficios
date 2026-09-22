<?php

namespace App\Mail;

use App\Models\Turnado;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OficioTurnadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Turnado $turnado;

    public bool $esResponsable;

    public function __construct(Turnado $turnado)
    {
        $this->turnado = $turnado;
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