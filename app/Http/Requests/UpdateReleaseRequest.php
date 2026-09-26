<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReleaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'in:anime,game,movie,tv_series,comic,manga,album,merchandise,other'],
            'release_date' => ['required', 'date'],
            'platform' => ['nullable', 'string', 'max:255'],
            'developer_publisher' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'status' => ['required', 'in:announced,upcoming,released,cancelled'],
            'tags' => ['nullable'],
            'cover_image' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', 'max:5120'],
            'description' => ['nullable', 'string', 'max:10000'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }
}
