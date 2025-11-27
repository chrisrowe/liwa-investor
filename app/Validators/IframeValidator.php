<?php

namespace App\Validators;

class IframeValidator extends \Illuminate\Validation\Validator {

    public function validateIframeMatch($attribute, $value, $parameters)
    {
        return preg_match('/<iframe[^>]*>\s*<\/iframe>/',$value) ? true : false;
    }

}