<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast; // Import Trait Toast dari Mary UI

new class extends Component
{
    use Toast; // Gunakan trait agar bisa panggil $this->success() atau $this->error()

    public $email = '';
    public $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->success('Selamat datang kembali!'); // Toast sukses
            return redirect()->intended('/dashboard');
        }

        // Toast error jika login gagal
        $this->error('Email atau password salah.', position: 'toast-bottom toast-end');
    }
};
?>

<div>
    {{-- Komponen Card dari Mary UI --}}
    <x-card title="Login Pegawai" shadow separator>
        <x-form wire:submit="login">
            {{-- Input menggunakan Mary UI --}}
            <x-input 
                label="Email" 
                wire:model="email" 
                icon="o-envelope" 
                inline 
            />

            <x-input 
                label="Password" 
                type="password" 
                wire:model="password" 
                icon="o-key" 
                inline 
            />

            <x-slot:actions>
                <x-button label="Masuk" type="submit" class="btn-primary" spinner="login" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>