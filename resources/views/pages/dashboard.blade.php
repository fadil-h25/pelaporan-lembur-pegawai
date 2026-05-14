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
            <livewire:dashboard.pegawai-stats />
            <livewire:dashboard.pegawai-recent-lemburs />
        @endif
    </div>
</x-layouts.app>
