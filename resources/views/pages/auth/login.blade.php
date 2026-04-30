<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Mary\Traits\Toast;

new #[Layout('components.layouts.auth')] class extends Component {
    use Toast;

    public string $email = '';
    public string $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->success('Selamat datang kembali!');
            return redirect()->intended('/dashboard');
        }

        $this->error('Email atau password salah.', position: 'toast-bottom toast-end');
    }
};
?>

<div class="flex min-h-screen">
    <!-- Kolom Kiri: Ilustrasi/Branding (Sembunyi di Mobile, Muncul di Desktop) -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-primary items-center justify-center overflow-hidden">
        <!-- Background Gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary to-indigo-900 opacity-90"></div>

        <!-- Konten di dalam Kolom Kiri -->
        <div class="relative z-10 text-white p-12 text-center flex flex-col items-center">
            <x-icon name="o-clock" class="w-24 h-24 mb-6" />
            <h1 class="text-5xl font-bold mb-4">Pelaporan Lembur</h1>
            <p class="text-xl text-primary-content/80 max-w-md">Sistem informasi pengelolaan dan pelaporan lembur pegawai
                secara efisien.</p>
        </div>
    </div>

    <!-- Kolom Kanan: Form Login -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-base-100">
        <div class="w-full max-w-md">

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-bold text-base-content">Selamat Datang 👋</h2>
                <p class="text-base-content/70 mt-2">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            <x-form wire:submit="login">
                <x-input label="Email Address" wire:model="email" icon="o-envelope" inline />

                <x-input label="Password" type="password" wire:model="password" icon="o-key" inline />

                <x-slot:actions>
                    <x-button label="Masuk" type="submit" class="btn-primary w-full mt-4" spinner="login" />
                </x-slot:actions>
            </x-form>

        </div>
    </div>
</div>
