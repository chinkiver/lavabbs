<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Api\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }
}
