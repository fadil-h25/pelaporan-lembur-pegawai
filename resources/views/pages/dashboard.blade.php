<x-layouts.app>

    <x-header title="Dashboard" subtitle="Selamat datang kembali di sistem pelaporan lembur" separator progress-indicator>
        <x-slot:actions>
            {{-- Ini akan otomatis muncul di sisi kanan (Justify Between) --}}
            {{-- Input Search Tumpul --}}
            <x-input wire:model.live.debounce.300ms="search" placeholder="Search..." icon="o-magnifying-glass"
                class="rounded-full" clearable />

            {{-- Tombol Tambah --}}
            <x-button icon="o-plus" class="btn-success text-white rounded-full" />
        </x-slot:actions>
    </x-header>

    {{-- Konten utama halaman --}}
    <div class="pt-2">
        <p>Ini adalah halaman dashboard.</p>
    </div>
</x-layouts.app>
