<?php

namespace App\Jobs;

use App\Conformite\Collecte\CollecteAdresse;
use App\Conformite\Collecte\CollecteArchive;
use App\Conformite\Collecte\CollecteRendu;
use App\Conformite\Moteur;
use App\Mail\BilanPret;
use App\Models\Analyse;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

/**
 * Une analyse, en arrière-plan. L'archive déposée est supprimée dès la fin,
 * qu'elle ait réussi ou non : seul le bilan est conservé.
 */
class AnalyserProjet implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct(public int $analyseId) {}

    public const DOSSIER = 'app/analyses';

    /** Le fichier de l'archive déposée pour cette analyse, sous un nom aléatoire. */
    public static function cheminArchive(Analyse $analyse): ?string
    {
        return $analyse->archive ? storage_path(self::DOSSIER.'/'.basename($analyse->archive)) : null;
    }

    public function handle(CollecteAdresse $adresse, CollecteArchive $archive, Moteur $moteur): void
    {
        $analyse = Analyse::find($this->analyseId);
        if ($analyse === null) {
            return;
        }

        $analyse->etat = 'en_cours';
        $analyse->save();
        $debut = microtime(true);

        try {
            $corpus = match (true) {
                $analyse->mode === 'adresse' && $analyse->rendu => app(CollecteRendu::class)->collecter($analyse->cible),
                $analyse->mode === 'adresse' => $adresse->collecter($analyse->cible),
                default => $archive->collecter((string) @file_get_contents((string) self::cheminArchive($analyse)), $analyse->cible),
            };

            $bilan = $moteur->analyser($corpus);
            $analyse->forceFill([
                'etat' => 'terminee',
                'niveau' => $bilan['niveau'],
                'bloquants' => $bilan['bloquants'],
                'majeurs' => $bilan['majeurs'],
                'mineurs' => $bilan['mineurs'],
                'resultats' => $bilan['resultats'],
                'perimetre' => $bilan['perimetre'],
                'terminee_le' => now(),
            ])->save();
        } catch (RuntimeException $e) {
            /* Les refus de l'outil (archive piégée, adresse privée…) sont
               écrits pour l'usager : on les lui rend tels quels. */
            $analyse->forceFill(['etat' => 'echec', 'erreur' => $e->getMessage(), 'terminee_le' => now()])->save();
        } catch (Throwable $e) {
            report($e);
            $analyse->forceFill(['etat' => 'echec', 'erreur' => "L'analyse a été interrompue par une erreur interne.", 'terminee_le' => now()])->save();
        } finally {
            if ($chemin = self::cheminArchive($analyse)) {
                @unlink($chemin);
            }
        }

        Log::info('Analyse de conformité', [
            'analyse' => $analyse->id,
            'compte' => $analyse->user_id,
            'mode' => $analyse->mode,
            'etat' => $analyse->etat,
            'duree' => round(microtime(true) - $debut, 1),
        ]);

        try {
            Mail::to($analyse->user->email)->send(new BilanPret($analyse));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
