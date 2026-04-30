<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\User;

new #[Layout('components.layouts.app')] class extends Component {
    // State untuk pencarian, otomatis sinkron ke URL browser
    #[Url]
    public string $search = '';

    // Property untuk pagination
    public int $perPage = 5;

    use WithPagination;

    // Reset pagination saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Reset pagination saat perPage berubah
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    // Logika pengambilan data (nantinya ganti dengan Query ke DB)
    public function users()
    {
        return User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);
    }

    // Hitung total user untuk stats
    public function totalUsers()
    {
        return User::count();
    }

    // Header tabel (bisa ditaruh di properti atau di dalam fungsi)
    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#'], ['key' => 'name', 'label' => 'Nama']];
    }
};
?>

<div>
    {{-- HEADER Pages --}}
    <x-header title="Manajemen User" subtitle="Selamat datang kembali di sistem pelaporan lembur">
        <x-slot:actions>

        </x-slot:actions>
    </x-header>

    {{-- STATS SECTION --}}
    <div class="flex flex-wrap gap-4 w-full mb-6">
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Laporan" :value="$this->users()->total()" desc="Data ditemukan" icon="o-document-text" />
        </div>
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Pengguna" :value="$this->totalUsers()" desc="User terdaftar" icon="o-user" />
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <x-card>
        <x-header title="Data Daftar Pengguna" subtitle="Total pengguna terdaftar: {{ $this->totalUsers() }}" separator
            progress-indicator>
            <x-slot:actions>
                <x-input wire:model.live.debounce.300ms="search" placeholder="Cari pengguna..."
                    icon="o-magnifying-glass" class="rounded-full !bg-white" clearable />
                <x-button icon="o-plus" class="btn-success text-white rounded-full" />
            </x-slot:actions>
        </x-header>
        <x-table :per-page-values="[3, 5, 10]" :per-page="$perPage" with-pagination :headers="$this->headers()" :rows="$this->users()"
            @row-click="alert('Kamu mengklik ' + $event.detail.name)" />
    </x-card>

</div>
