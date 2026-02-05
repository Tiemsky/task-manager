<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
  public function messages(): array
  {
    return [
      'name.required' => 'Le nom de la catégorie est obligatoire.',
      'name.string' => 'Le nom de la catégorie doit être une chaîne de caractères.',
      'name.max' => 'Le nom de la catégorie ne doit pas dépasser 255 caractères.',
      'color.string' => 'La couleur doit être une chaîne de caractères.',
      'color.max' => 'La couleur ne doit pas dépasser 7 caractères.',
      'color.regex' => 'La couleur doit être un code hexadécimal valide (ex: #ff0000).',
    ];
  }
}
