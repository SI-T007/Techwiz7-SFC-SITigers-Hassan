<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'type' => ['nullable', 'in:image,video,audio,trailer'],
            'media_type' => ['nullable', 'string', 'in:' . implode(',', array_keys(Media::MEDIA_TYPES))],
            'quality' => ['nullable', 'string', 'in:' . implode(',', Media::QUALITIES)],
            'duration' => ['nullable', 'string', 'max:32'],
            'language' => ['nullable', 'string', 'max:64'],
            'release_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'content_id' => ['nullable', 'exists:contents,id'],
            'description' => ['nullable', 'string', 'max:3000'],
            'external_url' => ['nullable', 'url', 'max:1000'],
            'youtube_url' => ['nullable', 'string', 'max:1000'],
            'file' => ['nullable', 'file', 'max:10240'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:2048'],
            'tags' => ['nullable'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,published,archived'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $type = $this->input('type');
        if (! $type) {
            $mediaType = $this->input('media_type', 'trailer');
            if (str_contains($mediaType, 'song') || str_contains($mediaType, 'ost')) {
                $type = 'audio';
            } elseif (str_contains($mediaType, 'trailer') || str_contains($mediaType, 'teaser')) {
                $type = 'trailer';
            } else {
                $type = 'video';
            }
        }

        $tags = $this->input('tags');
        if (is_string($tags)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        $this->merge([
            'type' => $type,
            'media_type' => $this->input('media_type', 'movie_trailer'),
            'quality' => $this->input('quality', 'HD'),
            'tags' => $tags,
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
