<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\UserService;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')] class extends Component {
    // State untuk pencarian, otomatis sinkron ke URL browser
    #[Url]
    public string $search = '';

    // Property untuk pagination
    public int $perPage = 5;

    use WithPagination, Toast;

    public bool $userModal = false;
    public bool $showPassword = false;
    
    public bool $detailModal = false;
    public ?\App\Models\User $selectedUser = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $nip = '';
    public string $golongan = '';
    public string $jabatan = '';
    public string $bagian = '';


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

    protected function service(): UserService
    {
        return app(UserService::class);
    }

    public function users()
    {
        // Secara paksa query hanya mengembalikan pengguna dengan role pegawai
        $paginator = $this->service()->filter($this->search, \App\UserRole::PEGAWAI->value, $this->perPage);
        
        $paginator->getCollection()->transform(function ($user, $key) use ($paginator) {
            $user->serial_number = ($paginator->currentPage() - 1) * $paginator->perPage() + $key + 1;
            return $user;
        });

        return $paginator;
    }

    public function totalUsers()
    {
        return $this->service()->totalUsers();
    }

    public function totalAdmin()
    {
        return $this->service()->countByRole(\App\UserRole::ADMIN);
    }

    public function totalOperator()
    {
        return $this->service()->countByRole(\App\UserRole::OPERATOR);
    }

    public function totalPegawai()
    {
        return $this->service()->countByRole(\App\UserRole::PEGAWAI);
    }

    public function headers(): array
    {
        return $this->service()->tableHeaders();
    }

    public function bagianOptions(): array
    {
        return collect(\App\Bagian::cases())->map(function ($bagian) {
            return ['id' => $bagian->value, 'name' => $bagian->label()];
        })->toArray();
    }

    public function saveUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'nip' => 'nullable|string|unique:users,nip',
            'golongan' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'bagian' => 'nullable|string',
        ]);

        $this->service()->createUser([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'nip' => $this->nip,
            'golongan' => $this->golongan,
            'jabatan' => $this->jabatan,
            'bagian' => $this->bagian,
        ]);

        $this->userModal = false;
        $this->reset(['name', 'email', 'password', 'nip', 'golongan', 'jabatan', 'bagian']);
        
        $this->success('Pegawai berhasil ditambahkan.');
    }

    public function showUser($id)
    {
        $this->selectedUser = \App\Models\User::find($id);
        $this->detailModal = true;
    }
};
?>

<div>
    {{-- HEADER Pages --}}
   <x-custom-header 
        title="Manajemen User" 
        subtitle="Selamat datang kembali di sistem pelaporan lembur" 
    />

    {{-- STATS SECTION --}}
    <div class="flex flex-wrap gap-4 w-full mb-6">
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Admin" :value="$this->totalAdmin()" desc="Total akun admin" icon="o-shield-check" />
        </div>

        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Pegawai" :value="$this->totalPegawai()" desc="Total akun pegawai" icon="o-users" />
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <x-card>
        <x-custom-table-header title="Data Daftar Pengguna" subtitle="Total pengguna ditemukan: {{ $this->users()->total() }}">

            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari pengguna..."
                icon="o-magnifying-glass" class="rounded-full !bg-white" clearable />
            <x-button icon="o-plus" class="btn-success text-white rounded-full" wire:click="$set('userModal', true)" />
        </x-custom-table-header>
        <x-table :per-page-values="[3, 5, 10]" per-page="perPage" with-pagination :headers="$this->headers()" :rows="$this->users()" @row-click="$wire.showUser($event.detail.id)" class="cursor-pointer hover:bg-base-200/50">
            @scope('cell_bagian', $user)
                {{ $user->bagian ? $user->bagian->label() : '-' }}
            @endscope
        </x-table>
    </x-card>

    <x-modal wire:model="userModal" title="Tambah Pegawai">
        <x-form wire:submit="saveUser">
            <x-input label="Nama" wire:model="name" required />
            <x-input label="Email" wire:model="email" type="email" required />
            <x-password label="Password" wire:model="password" right-icon="o-eye" required />
            <x-input label="NIP" wire:model="nip" />
            <x-input label="Golongan" wire:model="golongan" />
            <x-input label="Jabatan" wire:model="jabatan" />
            <x-select label="Bagian" wire:model="bagian" :options="$this->bagianOptions()" placeholder="Pilih Bagian" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="$set('userModal', false)" />
                <x-button label="Simpan" type="submit" class="btn-primary" spinner="saveUser" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="detailModal" title="Detail Pegawai">
        @if($selectedUser)
            <div class="space-y-4">
                <x-input label="Nama" :value="$selectedUser->name" readonly />
                <x-input label="Email" :value="$selectedUser->email" readonly />
                <x-input label="NIP" :value="$selectedUser->nip" readonly />
                <x-input label="Golongan" :value="$selectedUser->golongan" readonly />
                <x-input label="Jabatan" :value="$selectedUser->jabatan" readonly />
                <x-input label="Bagian" :value="$selectedUser->bagian ? $selectedUser->bagian->label() : '-'" readonly />
            </div>
            <x-slot:actions>
                <x-button label="Tutup" wire:click="$set('detailModal', false)" class="btn-primary" />
            </x-slot:actions>
        @endif
    </x-modal>

</div>
