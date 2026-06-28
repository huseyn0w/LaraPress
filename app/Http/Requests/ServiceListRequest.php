<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceListRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'services_action' => ['required', 'string', Rule::in(['delete', 'destroy', 'restore'])],
            'services' => ['required', 'array'],
        ];
    }
}
