<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Services\LemburService;
use App\Models\Lembur;
use Illuminate\Support\Facades\Auth;
use App\UserRole;

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

    public function cetak($type, $id)
    {
        $lembur = Lembur::findOrFail($id);
        $nomor = $this->service()->getNomorDatabase($lembur);
        return $this->service()->downloadCetak($type, $lembur, $nomor);
    }
};
?>

<div>
    <x-custom-header 
        title="Dokumen Lembur" 
        subtitle="Daftar pengajuan dokumen lembur pegawai" 
    />

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
            
            {{-- Custom Kolom Nomor --}}
            @scope('cell_nomor', $lembur)
                @php
                    $nomorDatabase = app(\App\Services\LemburService::class)->getNomorDatabase($lembur);
                @endphp
                <span class="text-error font-bold">{{ str_pad($nomorDatabase, 4, '0', STR_PAD_LEFT) }}</span>
            @endscope

            {{-- Kolom Aksi --}}
            @scope('actions', $lembur)
                <div class="flex gap-2">
                    {{-- Cetak --}}
                    <x-button label="SPK" wire:click="cetak('spk', {{ $lembur->id }})" class="btn-sm btn-success text-white" spinner />
                    <x-button label="LPJ" wire:click="cetak('lpj', {{ $lembur->id }})" class="btn-sm btn-info text-white" spinner />

                    {{-- Hanya tampilkan Edit dan Delete jika Admin --}}
                    @if(Auth::user()->role === \App\UserRole::ADMIN)
                        {{-- Edit --}}
                        <x-button icon="o-pencil" link="/lembur/{{ $lembur->id }}/edit" class="btn-sm btn-ghost text-blue-500" />
                        
                        {{-- Hapus --}}
                        <x-button icon="o-trash" wire:click="delete({{ $lembur->id }})" wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?" class="btn-sm btn-ghost text-red-500" spinner />
                    @endif
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
