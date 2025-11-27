<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
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
            'file' => 'required|file|mimetypes:video/x-flv,video/mp4,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,application/x-mpegURL|max:40000'
        ];
    }

    public function messages()
    {
        return [
            'file.mimetypes' => 'File should be video',
        ];
    }
}
