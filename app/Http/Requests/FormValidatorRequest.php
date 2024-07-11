<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormValidatorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'centre_origin_id' => 'required|string',
            'centre_destination_id' => 'required|different:field_one',
            'date_from' => 'required|after_or_equal:today',
            'date_to' => 'required|after_or_equal:today',
            'employee_id' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'centre_destination_id.different' => 'El Centro de Origen y Destino deber ser distintos',
            'date_from.after_or_equal' => 'Fecha de Inicio Inferior a la Actual',
            'date_to.after_or_equal' => 'Fecha de Fin Inferior a la Actual',
            'employee_id.required' => 'Formulario Incompleto', 
        ];
    }
}
