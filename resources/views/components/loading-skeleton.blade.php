@props([
    'type' => 'card', // 'card', 'text', 'category'
    'count' => 1,
])

@for($i = 0; $i < $count; $i++)
    @if($type === 'card')
        <div class="fh-card p-3 h-100">
            <div class="fh-skeleton" style="height: 160px; border-radius: var(--fh-radius-md); margin-bottom: 1rem;"></div>
            <div class="fh-skeleton fh-skeleton-title"></div>
            <div class="fh-skeleton fh-skeleton-text"></div>
            <div class="fh-skeleton fh-skeleton-text" style="width: 80%;"></div>
        </div>
    @elseif($type === 'category')
        <div class="fh-card p-4 h-100">
            <div class="fh-skeleton" style="width: 48px; height: 48px; border-radius: var(--fh-radius-md); margin-bottom: 1rem;"></div>
            <div class="fh-skeleton fh-skeleton-title" style="width: 70%;"></div>
            <div class="fh-skeleton fh-skeleton-text"></div>
            <div class="fh-skeleton fh-skeleton-text" style="width: 50%;"></div>
        </div>
    @else
        <div class="py-2">
            <div class="fh-skeleton fh-skeleton-title"></div>
            <div class="fh-skeleton fh-skeleton-text"></div>
            <div class="fh-skeleton fh-skeleton-text" style="width: 85%;"></div>
        </div>
    @endif
@endfor
