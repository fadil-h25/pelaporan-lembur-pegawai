<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Models\SystemSetting;

new #[Layout('components.layouts.app')] class extends Component {
    #[Validate('required|string|max:255')]
    public string $nama_kasek;

    #[Validate('required|string|max:255')]
    public string $nip_kasek;

    #[Validate('required|string|max:255')]
    public string $akhiran_surat;

    public function mount()
    {
        $settings = SystemSetting::getSettings();
        $this->nama_kasek = $settings->nama_kasek;
        $this->nip_kasek = $settings->nip_kasek;
        $this->akhiran_surat = $settings->akhiran_surat;
    }

    public function save()
    {
        $validated = $this->validate();

        $settings = SystemSetting::getSettings();
        $settings->update($validated);

        $this->success('Pengaturan sistem berhasil disimpan!');
    }
};
?>

<div>
    <x-custom-header title="Pengaturan Sistem" subtitle="Konfigurasi pengaturan global aplikasi" />

    <div class="max-w-2xl">
        <x-card title="Informasi Kepala Sekretariat" separator>
            <x-form wire:submit="save">
                <div class="grid grid-cols-1 gap-4">
                    <x-input label="Nama Kepala Sekretariat" wire:model="nama_kasek" required />
                    <x-input label="NIP Kepala Sekretariat" wire:model="nip_kasek" required />
                    <x-input label="Akhiran Surat" wire:model="akhiran_surat" placeholder="Contoh: /SPKL/SN/"
                        required />
                </div>

                <x-slot:actions>
                    <x-button label="Batal" link="/dashboard" icon="o-arrow-left" class="btn-ghost" />
                    <x-button label="Simpan" type="submit" icon="o-check" class="btn-success text-white"
                        spinner="save" />
                </x-slot:actions>
            </x-form>
        </x-card>

        <x-card title="Informasi Penting" class="mt-6">
            <div class="text-sm text-base-content/70 space-y-2">
                <p><strong>Nama Kepala Sekretariat:</strong> Nama lengkap kepala sekretariat yang akan muncul di dokumen
                    cetak.</p>
                <p><strong>NIP Kepala Sekretariat:</strong> Nomor Induk Pegawai kepala sekretariat.</p>
                <p><strong>Akhiran Surat:</strong> Format akhir nomor surat, contoh: <code>/SPKL/SN/</code> akan
                    menghasilkan nomor seperti <code>0001.0/SPKL/SN/05/2026</code></p>
            </div>
        </x-card>
    </div>
</div>
