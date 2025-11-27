<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResearchRequest extends FormRequest
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
            'file' => 'required|file|mimes:pdf,xlsx,ppt,doc,dot,png,jpg,jpeg,gif|max:40000',
        ];
    }

    public function messages()
    {
        return [
            'file.mimetypes' => 'Document should be pdf or image',
        ];
    }
}
