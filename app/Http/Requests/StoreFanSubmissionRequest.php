<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFanSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'category_id' => ['required', 'exists:categories,id'],
            'type' => ['required', 'in:fan_art,fan_fiction,fan_video,cosplay,other'],
            'body' => ['nullable', 'string', 'max:10000'],
            'content' => ['nullable', 'string', 'max:10000'],
            'featured_image' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', 'max:5120'],
            'media_file' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', 'max:5120'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'source_url' => ['nullable', 'url', 'max:1000'],
        ];
    }
}
