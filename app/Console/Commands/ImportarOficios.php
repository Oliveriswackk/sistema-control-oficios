<?php

namespace App\Console\Commands;

use App\Models\Oficio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportarOficios extends Command
{
    /**
     * php artisan app:importar-oficios
     */
    protected $signature = 'app:importar-oficios';

    protected $description = 'Importa oficios históricos desde un CSV';

    public function handle()
    {
        $path = storage_path('app/import/oficios.csv');

        if (!file_exists($path)) {

            $this->error("No se encontró el archivo:");
            $this->error($path);

            return Command::FAILURE;
        }

        $this->info('Leyendo CSV...');

        $handle = fopen($path, 'r');

        // Encabezados
        $headers = fgetcsv($handle);

        $contador = 0;

        DB::beginTransaction();

        try {

            while (($row = fgetcsv($handle)) !== false) {

                $data = array_combine(
                    $headers,
                    $row
                );

                Oficio::create([

                    'uuid' => (string) Str::uuid(),

                    'numero_oficio' => $data['numero_oficio'],
                    'consecutivo' => $data['consecutivo'],

                    'tipo_oficio_id' => $data['tipo_oficio_id'],
                    'estado_id' => $data['estado_id'],

                    'asunto' => $data['asunto'] ?: null,

                    'fecha_oficio' => $data['fecha_oficio'],
                    'fecha_recepcion' => $data['fecha_recepcion'],

                    'fecha_limite' =>
                        $data['fecha_limite'] ?: null,

                    'requiere_respuesta' =>
                        $data['requiere_respuesta'],

                    'es_sensible' =>
                        $data['es_sensible'],

                    'respuesta_a_oficio_id' =>
                        $data['respuesta_a_oficio_id'] ?: null,

                    'remitente_nombre' =>
                        $data['remitente_nombre'] ?: null,

                    'remitente_cargo' =>
                        $data['remitente_cargo'] ?: null,

                    'remitente_dependencia' =>
                        $data['remitente_dependencia'] ?: null,

                    'destinatario_nombre' =>
                        $data['destinatario_nombre'] ?: null,

                    'destinatario_cargo' =>
                        $data['destinatario_cargo'] ?: null,

                    'destinatario_dependencia' =>
                        $data['destinatario_dependencia'] ?: null,

                    'quien_elabora_nombre' =>
                        $data['quien_elabora_nombre'] ?: null,

                    'quien_elabora_cargo' =>
                        $data['quien_elabora_cargo'] ?: null,

                    'responsable_inicial_id' =>
                        $data['responsable_inicial_id'] ?: null,

                    'coordinacion_origen_id' =>
                        $data['coordinacion_origen_id'] ?: null,

                    'usuario_registro_id' =>
                        $data['usuario_registro_id'],

                    'link_documento' =>
                        $data['link_documento'] ?: null,
                ]);

                $contador++;
            }

            fclose($handle);

            DB::commit();

            $this->info(
                "Importación completada. {$contador} oficios cargados."
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {

            DB::rollBack();

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}