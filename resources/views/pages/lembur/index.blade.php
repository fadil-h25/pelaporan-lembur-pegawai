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

    #[Url]
    public string $hasNomor = '';

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

    public function updatedHasNomor()
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
        return $this->service()->filter($this->search, $this->perPage, $this->startDate, $this->endDate, $this->sort, $this->hasNomor);
    }

    public function totalTanpaNomor()
    {
        return $this->service()->totalTanpaNomor();
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

        return $this->service()->downloadCetak($type, $lembur);
    }

    public function exportExcel()
    {
        return $this->service()->exportExcel($this->search, $this->startDate, $this->endDate, $this->sort, $this->hasNomor);
    }
};
?>

<div>
    <x-custom-header title="Dokumen Lembur" subtitle="Daftar pengajuan dokumen lembur pegawai" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full mb-6">
        <x-custom-stat title="Lembur Ditemukan" :value="$this->lemburs()->total()" desc="Hasil pencarian & filter"
            icon="o-document-magnifying-glass" />
        <x-custom-stat title="Belum Ada Nomor" :value="$this->totalTanpaNomor()" desc="Dokumen tanpa nomor surat"
            icon="o-document-minus" />
        <x-custom-stat title="Jam Lembur (Bulan Ini)" :value="$this->totalJamBulanIni() . ' Jam'" desc="Total jam bulan berjalan"
            icon="o-clock" />
        <x-custom-stat title="Jam Lembur (Tahun Ini)" :value="$this->totalJamTahunIni() . ' Jam'" desc="Total jam tahun berjalan"
            icon="o-calendar" />
    </div>

    <x-card>
        <x-custom-table-header title="Data Dokumen Lembur"
            subtitle="Total dokumen lembur ditemukan: {{ $this->lemburs()->total() }}">
            <div class="flex gap-2 items-center">
                <x-button label="Export Excel" icon="o-arrow-down-tray" wire:click="exportExcel" class="btn-info text-white rounded-full" spinner />
                <x-button link="/lembur/create" icon="o-plus" class="btn-success text-white rounded-full" />
            </div>
        </x-custom-table-header>

        {{-- Filter Row --}}
        <div class="bg-base-200/50 p-4 rounded-xl mb-4 flex flex-wrap gap-4 items-center">
            <x-input wire:model.live.debounce.300ms="search" placeholder="Cari nama, nip..." icon="o-magnifying-glass" class="!bg-white min-w-[200px] flex-1 md:flex-none" clearable />

            <x-select wire:model.live="sort" :options="[
                ['id' => 'terbaru', 'name' => 'Tanggal Terbaru'],
                ['id' => 'terlama', 'name' => 'Tanggal Terlama'],
                ['id' => 'nomor_asc', 'name' => 'Nomor Kecil ke Besar'],
                ['id' => 'nomor_desc', 'name' => 'Nomor Besar ke Kecil'],
            ]" class="!bg-white min-w-[200px] flex-1 md:flex-none" />

            <x-select wire:model.live="hasNomor" :options="[
                ['id' => '', 'name' => 'Semua Status Nomor'],
                ['id' => 'yes', 'name' => 'Sudah Ada Nomor'],
                ['id' => 'no', 'name' => 'Belum Ada Nomor'],
            ]" class="!bg-white min-w-[200px] flex-1 md:flex-none" />

            <div class="flex gap-2 items-center w-full md:w-auto mt-2 md:mt-0">
                <x-input type="date" wire:model.live="startDate" class="!bg-white" />
                <span class="text-gray-500 font-bold">-</span>
                <x-input type="date" wire:model.live="endDate" class="!bg-white" />
            </div>
        </div>

        <x-table :per-page-values="[3, 5, 10]" per-page="perPage" with-pagination :headers="$this->headers()" :rows="$this->lemburs()">

            {{-- Custom Kolom Nomor --}}
            @scope('cell_nomor', $lembur)
                @php
                    $fullNomor = app(\App\Services\LemburService::class)->formatNomorSurat($lembur);
                @endphp
                <span class="text-error font-bold">{{ $fullNomor }}</span>
            @endscope

            {{-- Custom Kolom Tanggal Lembur --}}
            @scope('cell_tanggal_lembur', $lembur)
                {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->translatedFormat('d F Y') }}
            @endscope

            {{-- Kolom Aksi --}}
            @scope('actions', $lembur)
                <div class="flex gap-2">
                    {{-- Cetak --}}
                    <x-button label="SPK" wire:click="cetak('spk', {{ $lembur->id }})"
                        class="btn-sm btn-success text-white" spinner />
                    <x-button label="LPJ" wire:click="cetak('lpj', {{ $lembur->id }})"
                        class="btn-sm btn-info text-white" spinner />

                    {{-- Edit (Semua user bisa akses, walau non-admin cuma bisa edit dokumen) --}}
                    <x-button icon="o-pencil" link="/lembur/{{ $lembur->id }}/edit"
                        class="btn-sm btn-ghost text-blue-500" />

                    {{-- Hanya tampilkan Delete jika Admin atau Operator --}}
                    @php
                        $userRole =
                            Auth::user()->role instanceof \BackedEnum ? Auth::user()->role->value : Auth::user()->role;
                    @endphp
                    @if (in_array($userRole, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]))
                        {{-- Hapus --}}
                        <x-button icon="o-trash" wire:click="delete({{ $lembur->id }})"
                            wire:confirm="Apakah Anda yakin ingin menghapus dokumen ini?"
                            class="btn-sm btn-ghost text-red-500" spinner />
                    @endif
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
