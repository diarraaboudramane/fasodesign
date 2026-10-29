<?php

namespace App\Console\Commands;

use App\Jobs\AnalyserProjet;
use App\Models\Analyse;
use Illuminate\Console\Command;

/**
 * Les bilans sont gardés un an, puis supprimés. Une archive restée sur le
 * disque après une interruption du serveur est supprimée au passage.
 */
class PurgerAnalyses extends Command
{
    protected $signature = 'charte:purger-analyses';

    protected $description = 'Supprime les bilans de conformité de plus d\'un an et les archives orphelines.';

    public function handle(): int
    {
        $limite = now()->subDays(config('charte.conformite.conservation_jours'));
        $supprimees = Analyse::where('created_at', '<', $limite)->delete();

        $orphelines = 0;
        foreach (glob(storage_path(AnalyserProjet::DOSSIER).'/*.zip') ?: [] as $archive) {
            if (filemtime($archive) < time() - 6 * 3600) {
                @unlink($archive);
                $orphelines++;
            }
        }

        $this->info("{$supprimees} bilan(s) et {$orphelines} archive(s) orpheline(s) supprimés.");

        return self::SUCCESS;
    }
}
