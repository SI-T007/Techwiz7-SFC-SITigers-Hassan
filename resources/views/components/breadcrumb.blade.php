@props([
    'items' => [], // array of ['label' => '...', 'url' => '...']
])

<nav aria-label="breadcrumb">
    <ol class="fh-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="fh-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="{{ route('home') }}" itemprop="item">
                <i class="bi bi-house-door me-1"></i> <span itemprop="name">Home</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>

        @foreach($items as $index => $item)
            @if(!empty($item['url']) && !$loop->last)
                <li class="fh-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="{{ $item['url'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['label'] }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @else
                <li class="fh-breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $item['label'] }}</span>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @endif
        @endforeach
    </ol>
</nav>
