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
        // A autorização real é feita no UserController.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        $userId = $this->route('user'); 

        $idToIgnore = is_object($userId) ? $userId->id : $userId;


        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($idToIgnore)], // <-- Usar $idToIgnore aqui
            'role' => ['required', Rule::in(['admin', 'manager', 'user'])],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ];
    }
}
