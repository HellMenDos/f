<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'title'          => 'required|string|min:2|max:40',
            'description'    => 'max:500|string|nullable',
//            'price'          => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'price'          => 'required',
            'unit_id'        => 'required',
            'group_id'       => 'integer',
            'service_id'     => 'integer',
            'published'      => 'integer|nullable',
            'created_at'     => 'date_format:Y-m-d H:i:s',
            'updated_at'     => 'date_format:Y-m-d H:i:s',
            'execution_date' => 'date_format:Y-m-d H:i:s',
        ];
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();

        $data['title'] = filter_title($data['title']);
        $data['description'] = filter_fulltext($data['description']);


        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }

    public function messages()
    {
        return [

        ];
    }
}
