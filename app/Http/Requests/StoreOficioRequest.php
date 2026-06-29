<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOficioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'tipo_oficio_id' => ['required', 'integer'],

            'coordinacion_origen_id' => [
                'nullable',
                'required_if:tipo_oficio_id,1',
                'exists:coordinaciones,id',
            ],

            'numero_oficio' => ['required', 'string'],

            'asunto' => ['required', 'string'],
            'descripcion' => ['nullable', 'string'],

            'fecha_oficio' => ['required', 'date'],
            'fecha_recepcion' => ['required', 'date'],
            'fecha_limite' => ['nullable', 'date'],

            'requiere_respuesta' => ['boolean'],

            'respuesta_a_oficio_id' => ['nullable', 'integer'],

            'es_sensible' => ['boolean'],

            'remitente_nombre' => ['nullable', 'string'],
            'remitente_cargo' => ['nullable', 'string'],
            'remitente_dependencia' => ['nullable', 'string'],

            'destinatario_nombre' => ['nullable', 'string'],
            'destinatario_cargo' => ['nullable', 'string'],
            'destinatario_dependencia' => ['nullable', 'string'],

            'quien_elabora_nombre' => ['nullable', 'string'],
            'quien_elabora_cargo' => ['nullable', 'string'],

            'link_documento' => ['nullable', 'string'],
        ];
    }
}