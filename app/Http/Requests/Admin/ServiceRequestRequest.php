<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'quantity'        => 'integer|required',
            'execution_date'  => 'string|required',
            'service_id'      => 'integer|required',
            'execution_hours' => 'integer|required',
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();

        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function messages()
    {
        return [

        ];
    }
}
