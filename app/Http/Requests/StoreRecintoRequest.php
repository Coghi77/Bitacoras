<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecintoRequest extends FormRequest
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
        return [
            'nombre' => 'required|string|max:55',
            'institucion_id' => 'required|array|min:1',
            'institucion_id.*' => 'required|exists:institucione,id',
            'llave_id' => 'required|exists:llave,id|unique:recinto,llave_id',
            'estadoRecinto_id' => 'required|exists:estadorecinto,id',
            'tipoRecinto_id' => 'required|exists:tiporecinto,id',
        ];
    }
   
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del recinto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede exceder los 55 caracteres.',
            'nombre.unique' => 'Ya existe un recinto con este nombre.',
            
            'institucion_id.required' => 'Debe seleccionar al menos una institución.',
            'institucion_id.array' => 'Las instituciones deben ser válidas.',
            'institucion_id.min' => 'Debe seleccionar al menos una institución.',
            'institucion_id.*.required' => 'Cada institución debe ser válida.',
            'institucion_id.*.exists' => 'Una de las instituciones seleccionadas no es válida.',
            
            'llave_id.required' => 'El número de llave es obligatorio.',
            'llave_id.exists' => 'La llave seleccionada no es válida.',
            'llave_id.unique' => 'Esta llave ya está asignada a otro recinto.',
            
            'estadoRecinto_id.required' => 'El estado del recinto es obligatorio.',
            'estadoRecinto_id.exists' => 'El estado seleccionado no es válido.',
            
            'tipoRecinto_id.required' => 'El tipo de recinto es obligatorio.',
            'tipoRecinto_id.exists' => 'El tipo de recinto seleccionado no es válido.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_crear', true);
        
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}