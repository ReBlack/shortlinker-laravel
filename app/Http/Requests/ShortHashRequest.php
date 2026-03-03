<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortHashRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'shortHash' => $this->route('shortHash'),
        ]);
    }

    public function rules(): array
    {
        return [
            'shortHash' => ['required', 'regex:/^[a-zA-Z0-9]{8}$/'],
        ];
    }
}
