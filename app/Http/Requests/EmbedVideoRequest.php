<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmbedVideoRequest extends FormRequest
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
            'embeded' => 'required|iframe_match',
            'linkName' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'linkName.required' => 'File should be have name',
            'embeded.iframe_match' => 'File should be ifram tag',
        ];
    }
}
