<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lembur;
use App\Services\NomorSuratService;

class FillNomorSurat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lembur:isi-nomor {--dry-run : Do not persist changes, only show actions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Isi nomor surat untuk data lembur yang belum memiliki nomor (no_utama)';

    public function handle(NomorSuratService $nomorSuratService)
    {
        $query = Lembur::whereNull('no_utama')->orderBy('tanggal_lembur', 'asc');
        $total = $query->count();

        if ($total === 0) {
            $this->info('Tidak ada data lembur tanpa nomor.');
            return 0;
        }

        $this->info("Menemukan $total data tanpa nomor. Memproses...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($query->cursor() as $lembur) {
            $nomor = $nomorSuratService->generate($lembur->tanggal_lembur);

            if ($this->option('dry-run')) {
                $this->line("Lembur ID {$lembur->id}: akan diset no_utama={$nomor['no_utama']} no_sisipan={$nomor['no_sisipan']}");
            } else {
                $lembur->update([
                    'no_utama' => $nomor['no_utama'],
                    'no_sisipan' => $nomor['no_sisipan'],
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info('Selesai mengisi nomor surat.');

        return 0;
    }
}
