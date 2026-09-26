@extends('layouts.app')

@section('title', 'Edit Knowledge Entry: #' . $knowledge->id . ' — Chatbot CMS')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.chatbot.knowledge.index') }}">Chatbot KB</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit #{{ $knowledge->id }}</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-light mb-0">Edit Knowledge Base Entry</h1>
        </div>
        <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to KB
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger mb-4">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="fh-card p-4">
        <form method="POST" action="{{ route('admin.chatbot.knowledge.update', $knowledge) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="question" class="form-label text-light small fw-semibold">Target User Question / Topic *</label>
                    <input type="text" class="form-control bg-dark border-secondary text-light @error('question') is-invalid @enderror" id="question" name="question" value="{{ old('question', $knowledge->question) }}" required>
                </div>

                <div class="col-md-4">
                    <label for="category_id" class="form-label text-light small fw-semibold">Fandom Scope (Optional)</label>
                    <select class="form-select bg-dark border-secondary text-light @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                        <option value="">General Platform Knowledge</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $knowledge->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label for="answer" class="form-label text-light small fw-semibold">Verified Assistant Response *</label>
                    <textarea class="form-control bg-dark border-secondary text-light @error('answer') is-invalid @enderror" id="answer" name="answer" rows="6" required>{{ old('answer', $knowledge->answer) }}</textarea>
                    <div class="form-text text-muted small">Supports markdown links: [Label](https://url.com)</div>
                </div>

                @php
                    $keywordsStr = is_array($knowledge->keywords) ? implode(', ', $knowledge->keywords) : $knowledge->keywords;
                @endphp
                <div class="col-md-8">
                    <label for="keywords" class="form-label text-light small fw-semibold">Search Keywords (Comma-separated)</label>
                    <input type="text" class="form-control bg-dark border-secondary text-light @error('keywords') is-invalid @enderror" id="keywords" name="keywords" value="{{ old('keywords', $keywordsStr) }}">
                </div>

                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $knowledge->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label text-light" for="is_active">Publish Active</label>
                    </div>
                </div>

                <div class="col-12 mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Update Knowledge Entry</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
