<?php

use Livewire\Volt\Component;
use App\Models\Lembur;

new class extends Component {
    public function with(): array
    {
        return [
            'lemburs' => Lembur::with('user')->orderBy('created_at', 'desc')->take(5)->get()
        ];
    }
};
?>

<x-card title="Aktivitas Lembur Terbaru" shadow class="h-96 overflow-hidden flex flex-col">
    <div class="overflow-y-auto flex-1">
        <x-table :headers="[
            ['key' => 'user.name', 'label' => 'Pegawai'],
            ['key' => 'tanggal_lembur', 'label' => 'Tanggal'],
            ['key' => 'jumlah_jam', 'label' => 'Jam'],
        ]" :rows="$lemburs">
            @scope('cell_tanggal_lembur', $lembur)
                {{ \Carbon\Carbon::parse($lembur->tanggal_lembur)->format('d M Y') }}
            @endscope
            @scope('cell_jumlah_jam', $lembur)
                <x-badge :value="$lembur->jumlah_jam . ' Jam'" class="badge-neutral" />
            @endscope
        </x-table>
    </div>
    <div class="mt-4 text-center">
        <x-button label="Lihat Semua Data" link="/lembur" class="btn-ghost btn-sm w-full" />
    </div>
</x-card>
