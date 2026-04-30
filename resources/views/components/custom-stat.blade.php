@props(['title', 'value', 'desc' => null, 'icon' => null])

<div class="stats shadow w-full bg-base-100">
    <div class="stat">
        {{-- Jika ingin ada ikon --}}
        @if ($icon)
            <div class="stat-figure text-primary">
                <x-icon :name="$icon" class="w-8 h-8" />
            </div>
        @endif

        <div class="stat-title">{{ $title }}</div>
        <div class="stat-value text-primary">{{ $value }}</div>

        @if ($desc)
            <div class="stat-desc">{{ $desc }}</div>
        @endif
    </div>
</div>
