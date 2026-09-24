<?php

namespace App\Console\Commands;

use App\Models\NotificacionTurnado;
use Illuminate\Console\Command;

class DetectarRebotes extends Command
{
    protected $signature = 'sco:detectar-rebotes';

    protected $description = 'Detecta correos rebotados de las notificaciones del SCO';

    public function handle(): int
    {
        $client = new \Google\Client();

        $client->setApplicationName('SCO');
        $client->setScopes([
            \Google\Service\Gmail::GMAIL_READONLY,
        ]);
        $client->setAuthConfig(
            storage_path('app/google/client_secret.json')
        );
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $tokenPath = storage_path('app/google/token.json');

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(
                file_get_contents($tokenPath),
                true
            );

            $client->setAccessToken($accessToken);
        }

        if ($client->isAccessTokenExpired()) {

            if ($client->getRefreshToken()) {

                $client->fetchAccessTokenWithRefreshToken(
                    $client->getRefreshToken()
                );

                file_put_contents(
                    $tokenPath,
                    json_encode($client->getAccessToken())
                );

            } else {

                $authUrl = $client->createAuthUrl();

                $this->newLine();
                $this->info('Abre esta URL en tu navegador:');
                $this->newLine();
                $this->line($authUrl);
                $this->newLine();

                $this->info(
                    'Después de autorizar, copia SOLO el valor de "code" de la URL de localhost.'
                );

                $this->newLine();

                $code = $this->ask('Pega aquí el código');

                if (!$code) {
                    $this->error('No se recibió ningún código.');

                    return self::FAILURE;
                }

                $token = $client->fetchAccessTokenWithAuthCode(
                    $code
                );

                if (isset($token['error'])) {
                    $this->error(
                        'Google rechazó la autorización: ' .
                        ($token['error_description'] ?? $token['error'])
                    );

                    return self::FAILURE;
                }

                file_put_contents(
                    $tokenPath,
                    json_encode($token)
                );

                $this->info('Autorización guardada correctamente.');
            }
        }

        $gmail = new \Google\Service\Gmail($client);

        $response = $gmail->users_messages->listUsersMessages(
            'me',
            [
                'q' => 'from:(mailer-daemon) newer_than:7d',
                'maxResults' => 20,
            ]
        );

        $messages = $response->getMessages() ?? [];

        $this->info(
            'Correos de rebote encontrados: ' . count($messages)
        );

        $actualizados = 0;

        foreach ($messages as $message) {

            $detalle = $gmail->users_messages->get(
                'me',
                $message->getId(),
                [
                    'format' => 'full',
                ]
            );

            $inReplyTo = $this->buscarHeader(
    $detalle->getPayload(),
    'In-Reply-To'
);

$failedRecipient = $this->buscarHeader(
    $detalle->getPayload(),
    'X-Failed-Recipients'
);

$inReplyTo = trim(
    $inReplyTo ?? '',
    "<> \t\n\r\0\x0B"
);

$notificacion = null;

/*
|--------------------------------------------------------------------------
| 1. Intentar identificar por Message-ID
|--------------------------------------------------------------------------
*/
if ($inReplyTo !== '') {

    $notificacion = NotificacionTurnado::where(
        'message_id',
        $inReplyTo
    )->first();
}

/*
|--------------------------------------------------------------------------
| 2. Fallback: identificar por destinatario
|--------------------------------------------------------------------------
*/
if (!$notificacion && $failedRecipient) {

    $notificacion = NotificacionTurnado::where(
        'destinatario_email',
        trim($failedRecipient)
    )
    ->whereIn('estado', [
        NotificacionTurnado::ESTADO_EXITOSO,
        NotificacionTurnado::ESTADO_FALLIDO,
    ])
    ->latest('id')
    ->first();
}

if (!$notificacion) {
    $this->warn(
        'No se pudo asociar el rebote con una notificación del SCO.'
    );

    continue;
}

            if (!$notificacion) {
                continue;
            }

            if (
                $notificacion->estado ===
                NotificacionTurnado::ESTADO_NO_ENTREGADO
            ) {
                continue;
            }

            if (
                $notificacion->estado ===
                NotificacionTurnado::ESTADO_ENVIADO_MANUAL
            ) {
                continue;
            }

            $cuerpo = $this->extraerTexto(
                $detalle->getPayload()
            );

            $notificacion->update([
                'estado' => NotificacionTurnado::ESTADO_NO_ENTREGADO,
                'no_entregado_en' => now(),
                'ultimo_error' => mb_substr(
                    trim($cuerpo),
                    0,
                    5000
                ),
            ]);

            $actualizados++;

            $this->warn(
                "Notificación #{$notificacion->id} marcada como NO_ENTREGADO."
            );
        }

        $this->info(
            "Notificaciones actualizadas: {$actualizados}"
        );

        return self::SUCCESS;
    }

    private function buscarHeader(
        \Google\Service\Gmail\MessagePart $part,
        string $nombre
    ): ?string {
        foreach ($part->getHeaders() ?? [] as $header) {

            if (
                strcasecmp(
                    $header->getName(),
                    $nombre
                ) === 0
            ) {
                return $header->getValue();
            }
        }

        foreach ($part->getParts() ?? [] as $subparte) {

            $resultado = $this->buscarHeader(
                $subparte,
                $nombre
            );

            if ($resultado !== null) {
                return $resultado;
            }
        }

        return null;
    }

    private function extraerTexto(
        \Google\Service\Gmail\MessagePart $part
    ): string {
        $texto = '';

        $body = $part->getBody();

        if ($body && $body->getData()) {

            $data = $body->getData();

            $data = strtr(
                $data,
                '-_',
                '+/'
            );

            $data .= str_repeat(
                '=',
                (4 - strlen($data) % 4) % 4
            );

            $decoded = base64_decode(
                $data,
                true
            );

            if ($decoded !== false) {
                $texto .= $decoded;
            }
        }

        foreach ($part->getParts() ?? [] as $subparte) {
            $texto .= "\n" . $this->extraerTexto($subparte);
        }

        return $texto;
    }
}