<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['nullable', 'in:pending,in_progress,completed'],

        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'due_date.date' => 'La date d\'échéance doit être une date valide.',
            'due_date.after_or_equal' => 'La date d\'échéance doit être aujourd\'hui ou une date future.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'priority.required' => 'La priorité est obligatoire.',
            'priority.in' => 'La priorité doit être l\'une des valeurs suivantes : low, medium, high.',
            'status.in' => 'Le statut doit être l\'une des valeurs suivantes : pending, in_progress, completed.',
        ];
    }
}
