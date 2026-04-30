<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\LemburService;
use App\Models\Lembur;

new #[Layout('components.layouts.app')] class extends Component {
    #[Url]
    public string $search = '';

    public int $perPage = 5;

    use WithPagination;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    protected function service(): LemburService
    {
        return app(LemburService::class);
    }

    public function lemburs()
    {
        return $this->service()->filter($this->search, $this->perPage);
    }

    public function total()
    {
        return $this->service()->total();
    }

    public function headers(): array
    {
        return $this->service()->tableHeaders();
    }

    public function delete(Lembur $lembur)
    {
        $this->service()->delete($lembur);
    }
};
?>

<div>
    <x-header title="Dokumen Lembur" subtitle="Daftar pengajuan dokumen lembur pegawai" separator progress-indicator>
        <x-slot:actions>
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari data..." icon="o-magnifying-glass" class="rounded-full !bg-white" clearable />
            {{-- Mengarahkan ke halaman create --}}
            <x-button link="/lembur/create" icon="o-plus" class="btn-success text-white rounded-full" />
        </x-slot:actions>
    </x-header>

    <div class="flex flex-wrap gap-4 w-full mb-6">
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Lembur Ditemukan" :value="$this->lemburs()->total()" desc="Hasil pencarian & filter" icon="o-document-magnifying-glass" />
        </div>
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Total Lembur" :value="$this->total()" desc="Semua dokumen terdaftar" icon="o-document-text" />
        </div>
    </div>

    <x-card>
        <x-table :per-page-values="[3, 5, 10]" per-page="perPage" with-pagination :headers="$this->headers()" :rows="$this->lemburs()">
            
            {{-- Kolom Aksi --}}
            @scope('actions', $lembur)
                <div class="flex gap-2">
                    {{-- Edit --}}
                    <x-button icon="o-pencil" link="/lembur/{{ $lembur->id }}/edit" class="btn-sm btn-ghost text-blue-500" />
                    
                    {{-- Cetak --}}
                    <x-button icon="o-printer" link="{{ app(\App\Services\LemburService::class)->getPrintUrl($lembur) }}" class="btn-sm btn-ghost text-green-500" target="_blank" />
                    
                    {{-- Hapus --}}
                    <x-button icon="o-trash" wire:click="delete({{ $lembur->id }})" wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?" class="btn-sm btn-ghost text-red-500" />
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
