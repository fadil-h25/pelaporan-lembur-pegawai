<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Services\LemburService;
use App\Models\Lembur;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public Lembur $lembur;
    public bool $isAdmin = false;

    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|numeric|min:1')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran;

    #[Validate('required|string')]
    public $rencana_kerja;

    #[Validate('nullable|image|max:2048')]
    public $dokumentasi;

    public function mount(Lembur $lembur)
    {
        $this->lembur = $lembur;
        $this->isAdmin = Auth::user()->role === 'admin';
        
        $this->tanggal_lembur = $lembur->tanggal_lembur;
        $this->jumlah_jam = $lembur->jumlah_jam;
        $this->pembebanan_anggaran = $lembur->pembebanan_anggaran;
        $this->rencana_kerja = $lembur->rencana_kerja;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->dokumentasi) {
            $path = $this->dokumentasi->store('dokumentasi', 'local');
            $validated['dokumentasi'] = basename($path);
        }

        // Jika bukan admin, pastikan data yang readonly tidak diubah oleh request (keamanan)
        if (!$this->isAdmin) {
            unset($validated['tanggal_lembur']);
            unset($validated['jumlah_jam']);
            unset($validated['pembebanan_anggaran']);
            unset($validated['rencana_kerja']);
        }

        app(LemburService::class)->update($this->lembur, $validated);

        return redirect()->route('lembur.index');
    }
};
?>

<div>
    <x-custom-header 
        title="Edit Dokumen Lembur" 
        subtitle="Perbarui data pengajuan lembur" 
    />

    <x-card>
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama" value="{{ $lembur->nama }}" readonly />
                <x-input label="NIP" value="{{ $lembur->nip }}" readonly />
                <x-input label="Tanggal Lembur" wire:model="tanggal_lembur" type="date" required @if(!$isAdmin) readonly disabled @endif />
                <x-input label="Jumlah Jam" wire:model="jumlah_jam" type="number" required @if(!$isAdmin) readonly disabled @endif />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required @if(!$isAdmin) readonly disabled @endif />
            </div>
            
            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja" rows="4" required @if(!$isAdmin) readonly disabled @endif />

            <div class="mt-4">
                @if($lembur->dokumentasi)
                    <div class="mb-2">
                        <span class="text-sm text-gray-500">Dokumentasi Saat Ini:</span>
                        <div class="mt-1">
                            <img src="{{ route('private.dokumentasi', ['filename' => $lembur->dokumentasi]) }}" alt="Dokumentasi" class="h-32 object-cover rounded shadow">
                        </div>
                    </div>
                @endif
                <x-file label="Upload Dokumentasi Baru (Opsional)" wire:model="dokumentasi" accept="image/*" hint="Maksimal 2MB. Kosongkan jika tidak ingin mengubah." />
            </div>

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan Perubahan" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
