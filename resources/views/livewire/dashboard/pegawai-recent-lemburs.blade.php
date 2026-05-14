<?php

use Livewire\Volt\Component;
use App\Models\Lembur;

new class extends Component {
    public function with(): array
    {
        return [
            'lemburs' => Lembur::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];
    }
};
?>

<x-card title="Riwayat Lembur Terakhir Anda" shadow class="h-96 overflow-hidden flex flex-col">
    <div class="overflow-y-auto flex-1">
        <x-table :headers="[
            ['key' => 'tanggal_lembur', 'label' => 'Tanggal'],
            ['key' => 'jumlah_jam', 'label' => 'Jam'],
            ['key' => 'rencana_kerja', 'label' => 'Rencana Kerja'],
            ['key' => 'status_dokumen', 'label' => 'Status']
        ]" :rows="$lemburs">
            @scope('cell_tanggal_lembur', $lembur)
                {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}
            @endscope
            @scope('cell_jumlah_jam', $lembur)
                <x-badge :value="$lembur->jumlah_jam . ' Jam'" class="badge-neutral" />
            @endscope
            @scope('cell_status_dokumen', $lembur)
                @if($lembur->no_utama)
                    <x-badge value="Bernomor" class="badge-success badge-outline" />
                @else
                    <x-badge value="Menunggu" class="badge-warning badge-outline" />
                @endif
            @endscope
        </x-table>
    </div>
    <div class="mt-4 text-center">
        <x-button label="Lihat Semua Laporan Saya" link="/lembur" class="btn-ghost btn-sm w-full" />
    </div>
</x-card>
