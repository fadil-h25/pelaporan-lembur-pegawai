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

    #[Url]
    public string $startDate = '';

    #[Url]
    public string $endDate = '';

    #[Url]
    public string $sort = 'terbaru';

    public int $perPage = 5;

    use WithPagination;
    use \Mary\Traits\Toast;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedSort()
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
        return $this->service()->filter($this->search, $this->perPage, $this->startDate, $this->endDate, $this->sort);
    }

    public function totalJamTahunIni()
    {
        return $this->service()->totalJamTahunIni();
    }

    public function totalJamBulanIni()
    {
        return $this->service()->totalJamBulanIni();
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

        if ($type === 'lpj') {
            if (empty($lembur->hasil_kerja) || empty($lembur->dokumentasi)) {
                $this->warning('Harap isi Hasil Kerja dan Dokumentasi terlebih dahulu melalui fitur Edit!');
                return;
            }
        }

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
            <x-custom-stat title="Jam Lembur (Bulan Ini)" :value="$this->totalJamBulanIni() . ' Jam'" desc="Total jam bulan berjalan" icon="o-clock" />
        </div>
        <div class="flex-1 min-w-[200px]">
            <x-custom-stat title="Jam Lembur (Tahun Ini)" :value="$this->totalJamTahunIni() . ' Jam'" desc="Total jam tahun berjalan" icon="o-calendar" />
        </div>
    </div>

    <x-card>
        <x-custom-table-header title="Data Dokumen Lembur" subtitle="Total dokumen lembur ditemukan: {{ $this->lemburs()->total() }}">
            {{-- <x-input wire:model.live.debounce.300ms="search" placeholder="Cari data..." icon="o-magnifying-glass" class="rounded-full !bg-white" clearable /> --}}
            
            <div class="flex gap-2 items-center">
                <x-select wire:model.live="sort" :options="[
                    ['id' => 'terbaru', 'name' => 'Tanggal Terbaru'],
                    ['id' => 'terlama', 'name' => 'Tanggal Terlama'],
                    ['id' => 'nomor_asc', 'name' => 'Nomor Kecil ke Besar'],
                    ['id' => 'nomor_desc', 'name' => 'Nomor Besar ke Kecil'],
                ]" class="!bg-white min-w-[200px]" />

                <x-input type="date" wire:model.live="startDate" class="!bg-white" />
                <span class="text-gray-500 font-bold">-</span>
                <x-input type="date" wire:model.live="endDate" class="!bg-white" />
            </div>

            <x-button link="/lembur/create" icon="o-plus" class="btn-success text-white rounded-full" />
        </x-custom-table-header>
        <x-table :per-page-values="[3, 5, 10]" per-page="perPage" with-pagination :headers="$this->headers()" :rows="$this->lemburs()">
            
            {{-- Custom Kolom Nomor --}}
            @scope('cell_nomor', $lembur)
                @php
                    $nomorDatabase = app(\App\Services\LemburService::class)->getNomorDatabase($lembur);
                    $t = \Carbon\Carbon::parse($lembur->tanggal_lembur);
                    $fullNomor = str_pad($nomorDatabase, 4, '0', STR_PAD_LEFT) . '/SPKL/SN/' . $t->format('m/Y');
                @endphp
                <span class="text-error font-bold">{{ $fullNomor }}</span>
            @endscope

            {{-- Kolom Aksi --}}
            @scope('actions', $lembur)
                <div class="flex gap-2">
                    {{-- Cetak --}}
                    <x-button label="SPK" wire:click="cetak('spk', {{ $lembur->id }})" class="btn-sm btn-success text-white" spinner />
                    <x-button label="LPJ" wire:click="cetak('lpj', {{ $lembur->id }})" class="btn-sm btn-info text-white" spinner />

                    {{-- Edit (Semua user bisa akses, walau non-admin cuma bisa edit dokumen) --}}
                    <x-button icon="o-pencil" link="/lembur/{{ $lembur->id }}/edit" class="btn-sm btn-ghost text-blue-500" />

                    {{-- Hanya tampilkan Delete jika Admin atau Operator --}}
                    @if(in_array(Auth::user()->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]))
                        {{-- Hapus --}}
                        <x-button icon="o-trash" wire:click="delete({{ $lembur->id }})" wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?" class="btn-sm btn-ghost text-red-500" spinner />
                    @endif
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
