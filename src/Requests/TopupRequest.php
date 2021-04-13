<?php

namespace Hanoivip\GateClient\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|string',
            'dvalue' => 'required|integer|',
            'pid' => 'required|string',
            'sid' => 'string',
            'serial' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'string'
        ];
    }
}
