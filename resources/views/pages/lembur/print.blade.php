<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Lembur;

new #[Layout('components.layouts.auth')] class extends Component {
    public Lembur $lembur;

    public function mount(Lembur $lembur)
    {
        $this->lembur = $lembur;
    }
};
?>

<div class="p-10 max-w-4xl mx-auto bg-white text-black min-h-screen">
    {{-- Script untuk auto-print --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    
    <div class="text-center mb-10">
        <h1 class="text-2xl font-bold uppercase underline">Surat Perintah Lembur</h1>
        <p class="mt-2 text-sm font-semibold">Nomor: SPL-{{ str_pad($lembur->id, 5, '0', STR_PAD_LEFT) }}/{{ date('Y') }}</p>
    </div>

    <div class="mb-8 text-lg">
        <p class="mb-4">Yang bertanda tangan di bawah ini memberikan perintah lembur kepada:</p>
        <table class="w-full mb-4">
            <tr>
                <td class="w-1/3 py-2">Nama</td>
                <td class="w-4">:</td>
                <td class="font-bold">{{ $lembur->nama }}</td>
            </tr>
            <tr>
                <td class="py-2">NIP</td>
                <td>:</td>
                <td>{{ $lembur->nip }}</td>
            </tr>
            <tr>
                <td class="py-2">Pangkat/Golongan</td>
                <td>:</td>
                <td>{{ $lembur->golongan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="py-2">Jabatan</td>
                <td>:</td>
                <td>{{ $lembur->jabatan ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="mb-8 text-lg">
        <p class="mb-4">Untuk melaksanakan pekerjaan lembur dengan rincian sebagai berikut:</p>
        <table class="w-full mb-4">
            <tr>
                <td class="w-1/3 py-2">Tanggal Lembur</td>
                <td class="w-4">:</td>
                <td class="font-bold">{{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="py-2">Jumlah Jam</td>
                <td>:</td>
                <td>{{ $lembur->jumlah_jam }} Jam</td>
            </tr>
            <tr>
                <td class="py-2">Rencana Kerja</td>
                <td>:</td>
                <td>{{ $lembur->rencana_kerja }}</td>
            </tr>
            <tr>
                <td class="py-2">Pembebanan Anggaran</td>
                <td>:</td>
                <td>{{ $lembur->pembebanan_anggaran }}</td>
            </tr>
        </table>
    </div>

    <div class="mb-16 text-lg">
        <p>Demikian surat perintah lembur ini dibuat untuk dilaksanakan dengan sebaik-baiknya oleh pegawai yang bersangkutan.</p>
    </div>

    <div class="flex justify-end text-lg">
        <div class="text-center w-80">
            <p class="mb-24">Mengetahui,<br>Kepala Sub Bagian / Atasan Langsung</p>
            <p class="font-bold underline">{{ $lembur->nama_kasek ?? '(............................................)' }}</p>
            <p>NIP. {{ $lembur->nip_kasek ?? '............................................' }}</p>
        </div>
    </div>
    
    {{-- Tombol kembali untuk UI yang disembunyikan saat print --}}
    <div class="mt-10 print:hidden text-center">
        <x-button label="Kembali" link="/lembur" class="btn-primary" />
        <x-button label="Cetak Ulang" onclick="window.print()" class="btn-success text-white" />
    </div>
</div>
