<x-layouts.app>

    <x-header title="Manajemen User" subtitle="Selamat datang kembali di sistem pelaporan lembur" separator
        progress-indicator>
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
    <div class="flex flex-wrap gap-4 w-full">

        {{-- flex-1: agar tiap kotak punya lebar yang sama dan memenuhi ruang kosong --}}
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Laporan" :value="12" desc="Data terdaftar" icon="o-document-text" />
        </div>

        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Pengguna" :value="12" desc="Data terdaftar" icon="o-user" />
        </div>

    </div>

</x-layouts.app>
