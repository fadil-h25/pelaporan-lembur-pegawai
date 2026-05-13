<x-layouts.app>

   <x-custom-header 
        title="Dashboard" 
        subtitle="Selamat datang kembali, {{ auth()->user()->name }}" 
    />

    {{-- Konten utama halaman --}}
    <div class="pt-2 space-y-6">
        @if(auth()->user()->role->value === \App\UserRole::ADMIN->value || auth()->user()->role->value === \App\UserRole::OPERATOR->value)
            <livewire:dashboard.admin-stats />
            <livewire:dashboard.admin-charts />
            <livewire:dashboard.recent-lemburs />
        @else
            <x-card title="Dashboard Pegawai" shadow>
                <p>Selamat bekerja! Ringkasan lembur Anda akan tampil di sini.</p>
                <!-- Ruang untuk komponen pegawai di tahap selanjutnya -->
            </x-card>
        @endif
    </div>
</x-layouts.app>
