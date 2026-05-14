<?php

use Livewire\Volt\Component;
use App\Models\Lembur;
use Carbon\Carbon;
use App\Bagian;

new class extends Component {
    public array $topUsersChart = [];
    public array $bagianChart = [];

    public function mount()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // 1. Data Top 5 Pegawai (Bar Chart)
        $topUsers = Lembur::with('user')
            ->selectRaw('user_id, sum(jumlah_jam) as total_jam')
            ->whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->groupBy('user_id')
            ->orderByDesc('total_jam')
            ->take(5)
            ->get();

        $userLabels = $topUsers->map(fn($item) => $item->user->name ?? 'Unknown')->toArray();
        $userData = $topUsers->map(fn($item) => $item->total_jam)->toArray();

        $this->topUsersChart = [
            'type' => 'bar',
            'data' => [
                'labels' => empty($userLabels) ? ['Belum ada data'] : $userLabels,
                'datasets' => [
                    [
                        'label' => 'Total Jam Lembur',
                        'data' => empty($userData) ? [0] : $userData,
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

        // 2. Data per Bagian (Pie Chart)
        $lemburs = Lembur::with('user')
            ->whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->get();

        $bagianCount = [];
        foreach ($lemburs as $lembur) {
            $bagianValue = $lembur->user?->bagian?->value ?? 'Tidak Diketahui';
            $bagianLabel = $lembur->user?->bagian?->label() ?? $bagianValue;

            if (!isset($bagianCount[$bagianLabel])) {
                $bagianCount[$bagianLabel] = 0;
            }
            $bagianCount[$bagianLabel] += 1; 
        }

        $bagianLabels = array_keys($bagianCount);
        $bagianData = array_values($bagianCount);
        $colors = ['rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)'];

        $this->bagianChart = [
            'type' => 'pie',
            'data' => [
                'labels' => empty($bagianLabels) ? ['Belum ada data'] : $bagianLabels,
                'datasets' => [
                    [
                        'label' => 'Jumlah Laporan Lembur',
                        'data' => empty($bagianData) ? [0] : $bagianData,
                        'backgroundColor' => empty($bagianData) ? ['#cbd5e1'] : array_slice($colors, 0, count($bagianData)),
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Chart Bar: Top 5 Pegawai --}}
    <x-card title="Top 5 Pegawai Lembur Bulan Ini" shadow class="h-96">
        @if(empty($topUsersChart['data']['labels']) || $topUsersChart['data']['labels'][0] === 'Belum ada data')
            <div class="flex items-center justify-center h-full text-gray-500 pb-10">
                Belum ada data lembur di bulan ini.
            </div>
        @else
            <div class="relative h-64">
                <x-chart wire:model="topUsersChart" />
            </div>
        @endif
    </x-card>

    {{-- Chart Pie: Distribusi per Bagian --}}
    <x-card title="Laporan Lembur per Bagian" shadow class="h-96">
        @if(empty($bagianChart['data']['labels']) || $bagianChart['data']['labels'][0] === 'Belum ada data')
            <div class="flex items-center justify-center h-full text-gray-500 pb-10">
                Belum ada data lembur di bulan ini.
            </div>
        @else
            <div class="relative h-64 flex justify-center">
                <x-chart wire:model="bagianChart" />
            </div>
        @endif
    </x-card>
</div>
