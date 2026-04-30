<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\LemburService;
use App\Models\Lembur;

new #[Layout('components.layouts.app')] class extends Component {
    public Lembur $lembur;

    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|numeric|min:1')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran;

    #[Validate('required|string')]
    public $rencana_kerja;

    public function mount(Lembur $lembur)
    {
        $this->lembur = $lembur;
        $this->tanggal_lembur = $lembur->tanggal_lembur;
        $this->jumlah_jam = $lembur->jumlah_jam;
        $this->pembebanan_anggaran = $lembur->pembebanan_anggaran;
        $this->rencana_kerja = $lembur->rencana_kerja;
    }

    public function save()
    {
        $validated = $this->validate();

        app(LemburService::class)->update($this->lembur, $validated);

        return redirect()->route('lembur.index');
    }
};
?>

<div>
    <x-header title="Edit Dokumen Lembur" subtitle="Perbarui data pengajuan lembur" separator>
        <x-slot:actions>
            <x-button label="Kembali" link="/lembur" icon="o-arrow-left" class="btn-ghost" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama" value="{{ $lembur->nama }}" readonly />
                <x-input label="NIP" value="{{ $lembur->nip }}" readonly />
                <x-input label="Tanggal Lembur" wire:model="tanggal_lembur" type="date" required />
                <x-input label="Jumlah Jam" wire:model="jumlah_jam" type="number" required />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required />
            </div>
            
            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja" rows="4" required />

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan Perubahan" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
