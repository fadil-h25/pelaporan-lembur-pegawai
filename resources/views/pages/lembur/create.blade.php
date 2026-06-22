<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use App\Services\LemburService;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')] class extends Component {
    use Toast;

    #[Validate('required|date')]
    public $tanggal_lembur;

    #[Validate('required|integer|in:1,2,3')]
    public $jumlah_jam;

    #[Validate('required|string')]
    public $pembebanan_anggaran = '';

    #[Validate('required|string')]
    public $rencana_kerja;

    public $selected_user_id = null;
    public $selected_nip = '';

    public array $datepickerConfig = [
        'altInput' => true,
        'altFormat' => 'd/m/Y',
        'dateFormat' => 'Y-m-d',
    ];

    public function updatedSelectedUserId($value)
    {
        if ($value) {
            $user = \App\Models\User::find($value);
            $this->selected_nip = $user ? $user->nip : '';
        } else {
            $this->selected_nip = '';
        }
    }

    public function mount(): void
    {
        $this->pembebanan_anggaran = 'DIPA TA ' . date('Y');
    }

    public function with(): array
    {
        $isAdminOrOperator = in_array(Auth::user()->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]);
        
        return [
            'isAdminOrOperator' => $isAdminOrOperator,
            'pegawaiList' => $isAdminOrOperator ? \App\Models\User::where('role', \App\UserRole::PEGAWAI->value)->get() : [],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        // Validasi tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat
        $tanggalLemburPertama = \App\Models\Lembur::min('tanggal_lembur');
        if ($tanggalLemburPertama && $validated['tanggal_lembur'] < $tanggalLemburPertama) {
            $this->error('Tanggal lembur tidak boleh di bawah tanggal lembur pertama yang tercatat di sistem (' . \Carbon\Carbon::parse($tanggalLemburPertama)->translatedFormat('d F Y') . ')', position: 'toast-top toast-end');
            return;
        }

        $isAdminOrOperator = in_array(Auth::user()->role->value, [\App\UserRole::ADMIN->value, \App\UserRole::OPERATOR->value]);

        if ($isAdminOrOperator) {
            $this->validate(['selected_user_id' => 'required|exists:users,id'], [
                'selected_user_id.required' => 'Anda harus memilih pegawai terlebih dahulu.'
            ]);
            $targetUser = \App\Models\User::find($this->selected_user_id);
        } else {
            $targetUser = Auth::user();
        }

        // Add user info and default status
        $data = array_merge($validated, [
            'user_id' => $targetUser->id,
            'nama' => $targetUser->name,
            'nip' => $targetUser->nip,
            'golongan' => $targetUser->golongan ?? null,
            'jabatan' => $targetUser->jabatan ?? null,
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
                @if($isAdminOrOperator)
                    <x-select label="Pilih Pegawai" wire:model.live="selected_user_id" :options="$pegawaiList" option-value="id" option-label="name" placeholder="-- Pilih Pegawai --" required />
                    <x-input label="NIP" wire:model="selected_nip" placeholder="Pilih pegawai untuk melihat NIP" readonly disabled />
                @else
                    <x-input label="Nama" value="{{ Auth::user()->name }}" readonly />
                    <x-input label="NIP" value="{{ Auth::user()->nip }}" readonly />
                @endif
                <x-datepicker label="Tanggal Lembur" wire:model="tanggal_lembur" icon="o-calendar" :config="$datepickerConfig" required />
                <x-select label="Jumlah Jam" wire:model="jumlah_jam" :options="[
                    ['id' => 1, 'name' => '1 Jam'],
                    ['id' => 2, 'name' => '2 Jam'],
                    ['id' => 3, 'name' => '3 Jam'],
                ]" placeholder="-- Pilih Jumlah Jam --" required />
                <x-input label="Pembebanan Anggaran" wire:model="pembebanan_anggaran" required />
            </div>

            <x-textarea label="Rencana Kerja" wire:model="rencana_kerja"
                placeholder="Contoh: Menindaklanjuti arahan atasan..." rows="4" required />

            <x-slot:actions>
                <x-button label="Batal" link="/lembur" class="btn-ghost" />
                <x-button label="Simpan & Ajukan" type="submit" icon="o-paper-airplane" class="btn-success text-white"
                    spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
