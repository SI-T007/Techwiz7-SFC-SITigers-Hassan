<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:article,video,audio,image,trailer'],
            'status' => ['required', 'in:draft,published,archived'],
            'description' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'string'],
            'media_url' => ['nullable', 'url', 'max:1000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,webp,gif,jpg', 'max:2048'],
            'release_date' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
