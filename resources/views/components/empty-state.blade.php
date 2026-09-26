@props([
    'icon' => 'bi-inbox',
    'title' => 'No Records Found',
    'message' => 'There are no items currently available in this sector of the universe.',
    'actionText' => null,
    'actionUrl' => null,
])

<div class="fh-empty-state">
    <div class="fh-empty-icon">
        <i class="bi {{ $icon }}"></i>
    </div>
    <h4 class="h5 fw-bold text-light mb-2">{{ $title }}</h4>
    <p class="small text-muted mx-auto mb-4" style="max-width: 480px;">{{ $message }}</p>
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="fh-btn-primary">
            {{ $actionText }}
        </a>
    @endif
</div>
