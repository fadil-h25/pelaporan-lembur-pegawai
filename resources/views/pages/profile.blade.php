<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Profil hanya menampilkan data user saat ini
};
?>

<div>
    <x-custom-header title="Profil Pengguna" subtitle="Informasi detail akun Anda" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Avatar & Info Singkat --}}
        <div class="md:col-span-1">
            <x-card class="text-center flex flex-col items-center justify-center py-8">
                <x-avatar 
                    :placeholder="collect(explode(' ', auth()->user()->name))->map(fn($n) => $n[0])->take(2)->implode('')" 
                    class="!w-32 !h-32 mb-4 mx-auto text-4xl" 
                />
                <h2 class="text-2xl font-bold mt-4 text-base-content">{{ auth()->user()->name }}</h2>
                <p class="text-base-content/60">{{ auth()->user()->email }}</p>
                <div class="mt-4">
                    <x-badge :value="ucfirst(auth()->user()->role->value)" class="badge-primary" />
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: Detail Informasi --}}
        <div class="md:col-span-2">
            <x-card title="Detail Informasi" separator>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Nama Lengkap" value="{{ auth()->user()->name }}" readonly />
                    <x-input label="Email" value="{{ auth()->user()->email }}" readonly />
                    <x-input label="NIP" value="{{ auth()->user()->nip ?? '-' }}" readonly />
                    <x-input label="Jabatan" value="{{ auth()->user()->jabatan ?? '-' }}" readonly />
                    <x-input label="Golongan" value="{{ auth()->user()->golongan ?? '-' }}" readonly />
                    <x-input label="Role Akses" value="{{ ucfirst(auth()->user()->role->value) }}" readonly />
                </div>
                
                <x-slot:actions>
                    {{-- Opsional: Tambahkan tombol edit profil di sini jika diperlukan di masa depan --}}
                    <x-button label="Kembali" link="/dashboard" icon="o-arrow-left" class="btn-ghost" />
                </x-slot:actions>
            </x-card>
        </div>
    </div>
</div>
