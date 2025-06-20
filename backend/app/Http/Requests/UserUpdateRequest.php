<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        $userModel = $this->route('user');
        
        return Auth::check() && Auth::user()->can('update', $userModel);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Obtém o ID do utilizador que está sendo atualizado para a regra unique:ignore
        $userId = $this->route('user')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ];
    }
}
