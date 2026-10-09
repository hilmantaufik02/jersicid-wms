<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
        ];
    }

    public function messages(): array
    {
        return [
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp',
            'image.max' => 'Ukuran gambar maksimal 2MB',
            'image.dimensions' => 'Dimensi gambar tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitasi filename
        if ($this->hasFile('image')) {
            $file = $this->file('image');
            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $this->merge(['_safe_filename' => $safeName]);
        }
    }
}
