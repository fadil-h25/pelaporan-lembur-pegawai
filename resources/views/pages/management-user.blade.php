<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\UserService;

new #[Layout('components.layouts.app')] class extends Component {
    // State untuk pencarian, otomatis sinkron ke URL browser
    #[Url]
    public string $search = '';

    #[Url]
    public string $role = '';

    // Property untuk pagination
    public int $perPage = 5;

    use WithPagination;

    // Reset pagination saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

    // Reset pagination saat perPage berubah
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    protected function service(): UserService
    {
        return app(UserService::class);
    }

    public function users()
    {
        return $this->service()->filter($this->search, $this->role, $this->perPage);
    }

    public function roles(): array
    {
        return $this->service()->getAvailableRoles();
    }

    public function totalUsers()
    {
        return $this->service()->totalUsers();
    }

    public function headers(): array
    {
        return $this->service()->tableHeaders();
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
            <x-custom-stat title="Pengguna Ditemukan" :value="$this->users()->total()" desc="Hasil pencarian & filter" icon="o-users" />
        </div>
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Pengguna" :value="$this->totalUsers()" desc="Semua pengguna terdaftar" icon="o-users" />
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <x-card>
        <x-header title="Data Daftar Pengguna" subtitle="Total pengguna terdaftar: {{ $this->totalUsers() }}" separator
            progress-indicator>
            <x-slot:actions>
                <x-select wire:model.live="role" :options="$this->roles()" option-value="id" option-label="name" class="rounded-full bg-white" />
                <x-input wire:model.live.debounce.300ms="search" placeholder="Cari pengguna..."
                    icon="o-magnifying-glass" class="rounded-full !bg-white" clearable />
                <x-button icon="o-plus" class="btn-success text-white rounded-full" />
            </x-slot:actions>
        </x-header>
        <x-table :per-page-values="[3, 5, 10]" per-page="perPage" with-pagination :headers="$this->headers()" :rows="$this->users()"
            @row-click="alert('Kamu mengklik ' + $event.detail.name)" />
    </x-card>

</div>
