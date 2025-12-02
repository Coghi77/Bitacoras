<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $evento = $this->route('evento');
        $eventoId = $evento->id;
        return [
            'id_Bitacora' => 'required|exists:bitacora,id',
            'user_id' => 'required|exists:users,id',
            'fecha' => 'required|date',
            'observacion' => 'required|string|max:255',
            'prioridad' => 'required|string|in:baja,regular,media,alta',
            'confirmacion' => 'required|boolean',
            'descripcion' => 'required|string|max:255',
            'condicion' => 'required|boolean',
            'estado' => 'sometimes|string|in:en_espera,en_proceso,completado',
            'enviar_soporte' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prioridad.in' => 'La prioridad debe ser baja, regular, media o alta',
            'observacion.required' => 'La observación es obligatoria',
            'estado.in' => 'El estado debe ser en_espera, en_proceso o completado',
        ];
    }
}
