<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use App\Models\User;
use App\Bagian;
use Illuminate\Support\Facades\Hash;
use Mary\Traits\Toast;

new #[Layout('components.layouts.app')] class extends Component {
    use Toast;

    public ?User $targetUser = null;
    public bool $isOwnProfile = true;
    public bool $isEditing = false;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $email = '';

    #[Validate('nullable|string|max:255')]
    public ?string $nip = null;

    #[Validate('nullable|string|max:255')]
    public ?string $jabatan = null;

    #[Validate('nullable|string|max:255')]
    public ?string $golongan = null;

    #[Validate('nullable|string|max:50')]
    public ?string $bagian = null;

    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount(?User $user = null)
    {
        if ($user && $user->id !== auth()->id()) {
            $this->targetUser = $user;
            $this->isOwnProfile = false;
        } else {
            $this->targetUser = auth()->user();
            $this->isOwnProfile = true;
        }

        $this->name = $this->targetUser->name;
        $this->email = $this->targetUser->email;
        $this->nip = $this->targetUser->nip;
        $this->jabatan = $this->targetUser->jabatan;
        $this->golongan = $this->targetUser->golongan;
        $this->bagian = $this->targetUser->bagian?->value;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            // Reset to original values if cancelled
            $this->mount($this->targetUser);
            $this->password = null;
            $this->password_confirmation = null;
            $this->resetValidation();
        }
    }

    public function save()
    {
        $this->validate();

        if (!$this->isOwnProfile) {
            return;
        }

        $user = auth()->user();
        
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'nip' => $this->nip,
            'jabatan' => $this->jabatan,
            'golongan' => $this->golongan,
            'bagian' => $this->bagian,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->isEditing = false;
        $this->password = null;
        $this->password_confirmation = null;

        $this->success('Profil berhasil diperbarui.');
    }
};
?>

<div>
    <x-custom-header title="Profil Pengguna" subtitle="Informasi detail akun Anda" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Avatar & Info Singkat --}}
        <div class="md:col-span-1">
            <x-card shadow>
                <div class="flex flex-col items-center text-center py-4">
                    <div class="flex justify-center w-full">
                        <x-avatar :placeholder="collect(explode(' ', $targetUser->name))
                            ->map(fn($n) => $n[0])
                            ->take(2)
                            ->implode('')" class="!w-32 !h-32 mb-4 text-4xl border-4 border-primary/10" />
                    </div>

                    <h2 class="text-2xl font-bold mt-2 text-base-content">{{ $targetUser->name }}</h2>
                    <p class="text-base-content/60">{{ $targetUser->email }}</p>

                    <div class="mt-6 w-full border-t border-base-200 pt-4">
                        <x-badge :value="ucfirst($targetUser->role->value)" class="badge-primary badge-outline py-3 px-4" />
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: Detail Informasi --}}
        <div class="md:col-span-2">
            <x-form wire:submit="save">
                <x-card title="Detail Informasi" separator>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input label="Nama Lengkap" wire:model="name" :readonly="!$isEditing" />
                        <x-input label="Email" wire:model="email" :readonly="!$isEditing" />
                        <x-input label="NIP" wire:model="nip" :readonly="!$isEditing" />
                        <x-input label="Jabatan" wire:model="jabatan" :readonly="!$isEditing" />
                        <x-input label="Golongan" wire:model="golongan" :readonly="!$isEditing" />
                        <x-select label="Bagian" wire:model="bagian" :options="collect(App\Bagian::cases())->map(fn($b) => ['id' => $b->value, 'name' => $b->label()])" option-value="id" option-label="name" :disabled="!$isEditing" placeholder="Pilih Bagian..." />
                        <x-input label="Role Akses" value="{{ ucfirst($targetUser->role->value) }}" readonly />
                    </div>

                    @if($isEditing)
                        <div class="mt-4 border-t border-base-200 pt-4">
                            <h3 class="font-semibold text-lg mb-4">Ubah Password (Opsional)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input type="password" label="Password Baru" wire:model="password" />
                                <x-input type="password" label="Konfirmasi Password" wire:model="password_confirmation" />
                            </div>
                        </div>
                    @endif

                    <x-slot:actions>
                        @if($isOwnProfile)
                            @if($isEditing)
                                <x-button label="Batal" wire:click="toggleEdit" icon="o-x-mark" class="btn-ghost" />
                                <x-button label="Simpan" type="submit" icon="o-check" class="btn-primary" spinner="save" />
                            @else
                                <x-button label="Kembali" link="/dashboard" icon="o-arrow-left" class="btn-ghost" />
                                <x-button type="button" label="Edit Profil" wire:click="toggleEdit" icon="o-pencil" class="btn-primary" />
                            @endif
                        @else
                            <x-button label="Kembali" link="/management-user" icon="o-arrow-left" class="btn-primary" />
                        @endif
                    </x-slot:actions>
                </x-card>
            </x-form>
        </div>
    </div>
</div>
