<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use App\Services\LemburService;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads, Toast;

    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|numeric|min:1')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran = 'DIPA TA 2025';

    #[Validate('required|string')]
    public $rencana_kerja;

    #[Validate('nullable|image|max:2048')]
    public $dokumentasi;

    public function save()
    {
        $validated = $this->validate();

        // Validasi tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat
        $tanggalLemburPertama = \App\Models\Lembur::min('tanggal_lembur');
        if ($tanggalLemburPertama && $validated['tanggal_lembur'] < $tanggalLemburPertama) {
            $this->error('Tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat di sistem (' . \Carbon\Carbon::parse($tanggalLemburPertama)->translatedFormat('d F Y') . ')', position: 'toast-top toast-end');
            return;
        }

        $user = Auth::user();

        if ($this->dokumentasi) {
            $path = $this->dokumentasi->store('dokumentasi', 'local');
            $validated['dokumentasi'] = basename($path);
        }

        // Add user info and default status
        $data = array_merge($validated, [
            'user_id' => $user->id,
            'nama' => $user->name,
            'nip' => $user->nip,
            'golongan' => $user->golongan ?? null,
            'jabatan' => $user->jabatan ?? null,
            'status' => 'Menunggu',
        ]);

        app(LemburService::class)->create($data);

        return redirect()->route('lembur.index');
    }
};
?>

<div>
    <x-custom-header title="Buat Dokumen Lembur" subtitle="Isi form di bawah untuk mengajukan lembur" />

    <x-card>
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama" value="{{ Auth::user()->name }}" readonly />
                <x-input label="NIP" value="{{ Auth::user()->nip }}" readonly />
                <x-input label="Tanggal Lembur" wire:model="tanggal_lembur" type="date" required />
                <x-input label="Jumlah Jam" wire:model="jumlah_jam" type="number" placeholder="Contoh: 2" required />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required />
            </div>

            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja"
                placeholder="Contoh: Menindaklanjuti arahan atasan..." rows="4" required />

            <div class="mt-4">
                <x-file label="Dokumentasi (Opsional)" wire:model="dokumentasi" accept="image/*" hint="Maksimal 2MB" />
            </div>

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan & Ajukan" type="submit" icon="o-paper-airplane" class="btn-success text-white"
                    spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
