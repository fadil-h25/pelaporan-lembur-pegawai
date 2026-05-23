<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Services\LemburService;
use App\Models\Lembur;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads, Toast;

    public Lembur $lembur;
    public bool $isAdmin = false;
    public bool $confirmNomorModal = false;

    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|numeric|min:1')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran;

    #[Validate('required|string')]
    public $rencana_kerja;

    #[Validate('nullable|string')]
    public $hasil_kerja;

    #[Validate('nullable|image|max:2048')]
    public $dokumentasi;

    public function mount(Lembur $lembur)
    {
        $this->lembur = $lembur;

        $role = Auth::user()->role;
        $roleValue = $role instanceof \BackedEnum ? $role->value : $role;
        $this->isAdmin = in_array($roleValue, ['admin', 'operator']);

        $this->tanggal_lembur = $lembur->tanggal_lembur;
        $this->jumlah_jam = $lembur->jumlah_jam;
        $this->pembebanan_anggaran = $lembur->pembebanan_anggaran;
        $this->rencana_kerja = $lembur->rencana_kerja;
        $this->hasil_kerja = $lembur->hasil_kerja;
    }

    public function save()
    {
        $validated = $this->validate();

        // Validasi tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat
        // (hanya jika tanggal diubah)
        if (isset($validated['tanggal_lembur']) && $validated['tanggal_lembur'] !== $this->lembur->tanggal_lembur) {
            $tanggalLemburPertama = \App\Models\Lembur::where('id', '!=', $this->lembur->id)->min('tanggal_lembur');
            if ($tanggalLemburPertama && $validated['tanggal_lembur'] < $tanggalLemburPertama) {
                $this->error('Tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat di sistem (' . \Carbon\Carbon::parse($tanggalLemburPertama)->translatedFormat('d F Y') . ')', position: 'toast-top toast-end');
                return;
            }
        }

        if ($this->dokumentasi) {
            // Hapus gambar lama jika ada (opsional, tapi disarankan agar storage tidak penuh)
            if ($this->lembur->dokumentasi && \Illuminate\Support\Facades\Storage::disk('local')->exists('dokumentasi/' . $this->lembur->dokumentasi)) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete('dokumentasi/' . $this->lembur->dokumentasi);
            }

            $path = $this->dokumentasi->store('dokumentasi', 'local');
            $validated['dokumentasi'] = basename($path);
        } else {
            // Jika user tidak mengupload gambar baru, jangan update kolom dokumentasi (jangan jadikan null)
            unset($validated['dokumentasi']);
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

    public function generateNomorSurat()
    {
        app(LemburService::class)->generateNomor($this->lembur);
        $this->lembur = $this->lembur->fresh();
        $this->confirmNomorModal = false;
        $this->success('Nomor surat berhasil digenerate.');
    }
};
?>

<div>
    <x-custom-header title="Edit Dokumen Lembur" subtitle="Perbarui data pengajuan lembur" />

    <x-card>
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(is_null($lembur->no_utama))
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-input label="Nomor Surat" value="Belum ada nomor" readonly disabled />
                        </div>
                        <x-button label="Ambil Nomor" @click="$wire.confirmNomorModal = true" class="btn-primary" type="button" />
                    </div>
                @else
                    <x-input label="Nomor Surat" value="{{ app(\App\Services\LemburService::class)->formatNomorSurat($lembur) }}" readonly disabled />
                @endif
                <x-input label="Nama" value="{{ $lembur->nama }}" readonly />
                <x-input label="NIP" value="{{ $lembur->nip }}" readonly />
                <x-input label="Tanggal Lembur" wire:model="tanggal_lembur" type="date" required :readonly="!$isAdmin"
                    :disabled="!$isAdmin" />
                <x-input label="Jumlah Jam" wire:model="jumlah_jam" type="number" required :readonly="!$isAdmin"
                    :disabled="!$isAdmin" />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required :readonly="!$isAdmin"
                    :disabled="!$isAdmin" />
            </div>

            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja" rows="4" required :readonly="!$isAdmin"
                :disabled="!$isAdmin" />

            <x-textarea label="Hasil Kerja" wire:model="hasil_kerja" rows="4"
                placeholder="Tuliskan hasil pekerjaan lembur Anda di sini..." />

            <div class="mt-4">
                @if ($lembur->dokumentasi)
                    <div class="mb-2">
                        <span class="text-sm text-gray-500">Dokumentasi Saat Ini:</span>
                        <div class="mt-1">
                            <img src="{{ route('private.dokumentasi', ['filename' => $lembur->dokumentasi]) }}"
                                alt="Dokumentasi" class="h-32 object-cover rounded shadow">
                        </div>
                    </div>
                @endif
                <x-file label="Upload Dokumentasi Baru (Opsional)" wire:model="dokumentasi" accept="image/*"
                    hint="Maksimal 2MB. Kosongkan jika tidak ingin mengubah." />
            </div>

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan Perubahan" type="submit" icon="o-check" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>

    <x-modal wire:model="confirmNomorModal" title="Peringatan Penting!" separator>
        <div x-data="{ timer: 3, interval: null }"
             x-init="
                $watch('$wire.confirmNomorModal', value => {
                    if(value) {
                        timer = 3;
                        if(interval) clearInterval(interval);
                        interval = setInterval(() => {
                            if(timer > 0) timer--;
                            else clearInterval(interval);
                        }, 1000);
                    } else {
                        if(interval) clearInterval(interval);
                    }
                })
             ">
            <div class="py-4 text-base">
                <p>Pastikan surat sudah <b>ditandatangani oleh pimpinan</b>.</p>
                <p class="text-error font-bold mt-2">Surat yang sudah terbit nomornya tidak bisa diedit kembali.</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <x-button label="Batal" @click="$wire.confirmNomorModal = false" class="btn-ghost" />
                <button 
                    type="button"
                    wire:click="generateNomorSurat" 
                    class="btn btn-primary" 
                    x-bind:disabled="timer > 0"
                >
                    <span x-show="timer > 0" x-text="`Tunggu (${timer} detik)...`"></span>
                    <span x-show="timer === 0">Saya Mengerti, Generate Nomor</span>
                </button>
            </div>
        </div>
    </x-modal>
</div>
