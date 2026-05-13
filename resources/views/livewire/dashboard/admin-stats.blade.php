<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Lembur;
use App\UserRole;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalLembur = Lembur::whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->count();

        $totalJam = Lembur::whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->sum('jumlah_jam');

        $totalPegawai = User::where('role', UserRole::PEGAWAI->value)->count();

        $dokumenBernomor = Lembur::whereMonth('tanggal_lembur', $currentMonth)
            ->whereYear('tanggal_lembur', $currentYear)
            ->whereNotNull('no_utama')
            ->where('no_utama', '!=', '')
            ->count();

        return [
            'totalLembur' => $totalLembur,
            'totalJam' => $totalJam,
            'totalPegawai' => $totalPegawai,
            'dokumenBernomor' => $dokumenBernomor,
        ];
    }
};
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <x-stat title="Lembur Bulan Ini" value="{{ $totalLembur }}" icon="o-document-text" class="shadow-sm" color="text-primary" />
    <x-stat title="Total Jam Lembur" value="{{ $totalJam }} Jam" icon="o-clock" class="shadow-sm" color="text-secondary" />
    <x-stat title="Dokumen Bernomor" value="{{ $dokumenBernomor }}" icon="o-check-badge" class="shadow-sm" color="text-success" />
    <x-stat title="Total Pegawai" value="{{ $totalPegawai }}" icon="o-users" class="shadow-sm" color="text-info" />
</div>
