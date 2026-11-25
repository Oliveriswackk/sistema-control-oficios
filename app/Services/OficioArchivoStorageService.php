<?php

namespace App\Services;

use App\Models\Oficio;
use App\Models\OficioArchivoVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class OficioArchivoStorageService
{
    private const DISCO_PUBLICO = 'public';

    private const DISCO_PRIVADO = 'local';

    private const PREFIJO_SENSIBLES = 'oficios-sensibles';

    public function diskFor(Oficio $oficio): string
    {
        return $oficio->es_sensible
            ? self::DISCO_PRIVADO
            : self::DISCO_PUBLICO;
    }

    public function diskForVersion(
        OficioArchivoVersion $version
    ): string {
        if ($this->isPrivatePath($version->ruta)) {
            return self::DISCO_PRIVADO;
        }

        return self::DISCO_PUBLICO;
    }

    public function storeUploaded(UploadedFile $file, Oficio $oficio): string
    {
        if (!$oficio->es_sensible) {
            return $file->store(
                "oficios/{$oficio->id}",
                self::DISCO_PUBLICO
            );
        }

        return $file->storeAs(
            $this->sensitiveDirectory($oficio),
            Str::uuid() . '.pdf',
            self::DISCO_PRIVADO
        );
    }

    public function putHistorical(
        string $contents,
        string $originalName,
        Oficio $oficio
    ): string {
        $disk = $this->diskFor($oficio);
        $path = $oficio->es_sensible
            ? $this->sensitiveDirectory($oficio) . '/' . Str::uuid() . '.pdf'
            : "oficios/{$oficio->id}/" . basename($originalName);

        if (!Storage::disk($disk)->put($path, $contents)) {
            throw new RuntimeException(
                "No fue posible guardar el PDF: {$originalName}"
            );
        }

        return $path;
    }

    public function isPrivatePath(string $path): bool
    {
        return Str::startsWith(
            $path,
            self::PREFIJO_SENSIBLES . '/'
        );
    }

    private function sensitiveDirectory(Oficio $oficio): string
    {
        $tipoClave = $oficio->tipo?->clave
            ?? $oficio->tipo()->value('clave');

        if ($tipoClave === 'enviado') {
            $direccion = 'enviados';
            $fecha = $oficio->fecha_oficio;
        } elseif (in_array($tipoClave, ['recibido', 'recibido_cpc'], true)) {
            $direccion = 'recibidos';
            $fecha = $oficio->fecha_recepcion;
        } else {
            throw new InvalidArgumentException(
                'No se puede determinar la dirección del oficio sensible.'
            );
        }

        if (!$fecha) {
            throw new InvalidArgumentException(
                'No se puede determinar el periodo del archivo sensible.'
            );
        }

        return sprintf(
            '%s/%s/%s/%s',
            self::PREFIJO_SENSIBLES,
            $fecha->format('Y'),
            $direccion,
            $fecha->format('m')
        );
    }
}
