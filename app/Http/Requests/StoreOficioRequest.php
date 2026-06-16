<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_oficio' => ['required', 'string'],
            'consecutivo' => ['nullable', 'integer'],

            'tipo_oficio_id' => ['nullable', 'integer'],
            'estado_id' => ['nullable', 'integer'],

            'asunto' => ['required', 'string'],
            'descripcion' => ['nullable', 'string'],

            'fecha_oficio' => ['nullable', 'date'],
            'fecha_recepcion' => ['nullable', 'date'],
            'fecha_limite' => ['nullable', 'date'],

            'requiere_respuesta' => ['boolean'],
            'respuesta_a_oficio_id' => ['required'],
            'es_sensible' => ['boolean'],

            'remitente_nombre' => ['nullable', 'string'],
            'remitente_cargo' => ['nullable', 'string'],
            'remitente_dependencia' => ['nullable', 'string'],

            'destinatario_nombre' => ['nullable', 'string'],
            'destinatario_cargo' => ['nullable', 'string'],
            'destinatario_dependencia' => ['nullable', 'string'],

            'quien_elabora_nombre' => ['nullable', 'string'],
            'quien_elabora_cargo' => ['nullable', 'string'],

            'coordinacion_origen_id' => ['nullable', 'integer'],
            'link_documento' => ['nullable', 'string'],
        ];
    }
}