<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    {{-- Sisi Kiri: Judul & Subjudul --}}
    <div>
        <h1 class="text-2xl font-bold text-base-content tracking-tight">
            {{ $title }}
        </h1>
        @if (isset($subtitle))
            <p class="text-base-content/60 text-sm mt-1">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    {{-- Sisi Kanan: Slot untuk Button/Action lainnya --}}
    <div class="flex items-center gap-2">
        {{ $actions ?? '' }}
    </div>
</div>
