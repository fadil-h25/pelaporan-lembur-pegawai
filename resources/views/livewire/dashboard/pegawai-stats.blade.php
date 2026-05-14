<?php

use Livewire\Volt\Component;
use App\Models\Lembur;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        $userId = auth()->id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalLembur = Lembur::where('user_id', $userId)
            ->whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->count();

        $totalJam = Lembur::where('user_id', $userId)
            ->whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->sum('jumlah_jam');

        $dokumenBernomor = Lembur::where('user_id', $userId)
            ->whereNotNull('no_utama')
            ->where('no_utama', '!=', '')
            ->count();

        return [
            'totalLembur' => $totalLembur,
            'totalJam' => $totalJam,
            'dokumenBernomor' => $dokumenBernomor,
        ];
    }
};
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <x-stat title="Laporan Saya (Bulan Ini)" value="{{ $totalLembur }}" icon="o-document-text" class="shadow-sm" color="text-primary" />
    <x-stat title="Total Jam Saya (Bulan Ini)" value="{{ $totalJam }} Jam" icon="o-clock" class="shadow-sm" color="text-secondary" />
    <x-stat title="Dokumen Disetujui/Bernomor" value="{{ $dokumenBernomor }}" icon="o-check-badge" class="shadow-sm" color="text-success" />
</div>
