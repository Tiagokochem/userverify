<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem auth por enquanto
    }

    public function rules(): array
    {
        return [
            'cpf' => ['required', 'digits:11'],
            'cep' => ['required', 'digits:8'],
            'email' => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos.',
            'cep.required' => 'O CEP é obrigatório.',
            'cep.digits' => 'O CEP deve conter exatamente 8 dígitos.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
        ];
    }
}
