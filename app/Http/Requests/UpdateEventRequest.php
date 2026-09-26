<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'event_type' => ['required', 'in:convention,cosplay_meetup,screening,livestream,release_event,concert,other'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'venue_name' => ['nullable', 'string', 'max:500'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'ticket_url' => ['nullable', 'url', 'max:1000'],
            'cover_image' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,gif', 'max:5120'],
            'description' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', 'in:upcoming,ongoing,completed,cancelled'],
            'is_featured' => ['nullable', 'boolean'],
            'is_online' => ['nullable', 'boolean'],
        ];
    }
}
