<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\LemburService;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.app')] class extends Component {
    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|numeric|min:1')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran = 'DIPA TA 2025';

    #[Validate('required|string')]
    public $rencana_kerja;

    public function save()
    {
        $validated = $this->validate();
        
        $user = Auth::user();

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
    <x-custom-header 
        title="Buat Dokumen Lembur" 
        subtitle="Isi form di bawah untuk mengajukan lembur" 
    />

    <x-card>
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama" value="{{ Auth::user()->name }}" readonly />
                <x-input label="NIP" value="{{ Auth::user()->nip }}" readonly />
                <x-input label="Tanggal Lembur" wire:model="tanggal_lembur" type="date" required />
                <x-input label="Jumlah Jam" wire:model="jumlah_jam" type="number" placeholder="Contoh: 2" required />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required />
            </div>
            
            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja" placeholder="Contoh: Menindaklanjuti arahan atasan..." rows="4" required />

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan & Ajukan" type="submit" icon="o-paper-airplane" class="btn-success text-white" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
