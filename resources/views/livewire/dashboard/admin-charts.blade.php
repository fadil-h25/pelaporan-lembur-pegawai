<?php

use Livewire\Volt\Component;
use App\Models\Lembur;
use Carbon\Carbon;

new class extends Component {
    public array $chartData = [];

    public function mount()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $topUsers = Lembur::with('user')
            ->selectRaw('user_id, sum(jumlah_jam) as total_jam')
            ->whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->groupBy('user_id')
            ->orderByDesc('total_jam')
            ->take(5)
            ->get();

        $labels = $topUsers->map(fn($item) => $item->user->name ?? 'Unknown')->toArray();
        $data = $topUsers->map(fn($item) => $item->total_jam)->toArray();

        $this->chartData = [
            'type' => 'bar',
            'data' => [
                'labels' => empty($labels) ? ['Belum ada data'] : $labels,
                'datasets' => [
                    [
                        'label' => 'Total Jam Lembur',
                        'data' => empty($data) ? [0] : $data,
                        'backgroundColor' => 'rgba(74, 0, 224, 0.5)',
                        'borderColor' => 'rgb(74, 0, 224)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }
};
?>

<x-card title="Top 5 Pegawai Lembur Bulan Ini" shadow class="h-96">
    @if(empty($chartData['data']['labels']) || $chartData['data']['labels'][0] === 'Belum ada data')
        <div class="flex items-center justify-center h-full text-gray-500 pb-10">
            Belum ada data lembur di bulan ini.
        </div>
    @else
        <div class="relative h-64">
            <x-chart wire:model="chartData" />
        </div>
    @endif
</x-card>
