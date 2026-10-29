<?php

namespace App\Conformite;

use App\Models\Analyse;

/**
 * Le bilan en Markdown : à verser au dépôt du projet, ou à confier avec
 * charte.md à un assistant de code pour qu'il propose les corrections.
 */
final class BilanMarkdown
{
    public const GRAVITES = ['bloquante' => 'Bloquante', 'majeure' => 'Majeure', 'mineure' => 'Mineure'];

    public static function ecrire(Analyse $analyse): string
    {
        $p = $analyse->perimetre ?? [];
        $l = [];

        $l[] = '# Bilan de conformité à la charte graphique';
        $l[] = '';
        $l[] = '| | |';
        $l[] = '|---|---|';
        $l[] = '| Cible | '.self::cellule($analyse->cible).' |';
        $l[] = '| Mode | '.$analyse->libelleMode().' |';
        $l[] = '| Date | '.$analyse->terminee_le?->format('d/m/Y H:i').' |';
        $l[] = '| Charte de référence | '.$analyse->version_charte.' |';
        $l[] = '| Niveau | **'.$analyse->libelleNiveau().'** |';
        $l[] = '| Règles non respectées | '.$analyse->bloquants.' bloquante(s), '.$analyse->majeurs.' majeure(s), '.$analyse->mineurs.' mineure(s) |';
        $l[] = '';

        foreach (['non_respectee' => 'Écarts', 'non_verifiable' => 'Non vérifiable', 'respectee' => 'Règles respectées'] as $statut => $titre) {
            $resultats = array_filter($analyse->resultats ?? [], fn ($r) => $r['statut'] === $statut);
            if (! $resultats) {
                continue;
            }
            $l[] = '## '.$titre;
            $l[] = '';
            foreach ($resultats as $r) {
                $l[] = '### '.$r['titre'].($statut === 'non_respectee' ? ' — '.self::GRAVITES[$r['gravite']] : '');
                $l[] = '';
                if ($statut === 'non_respectee') {
                    $l[] = 'À faire : '.$r['correction'].' Voir '.Reference::adresse($r['lien'], true).'.';
                    $l[] = '';
                    foreach ($r['ecarts'] as $e) {
                        $l[] = '- `'.$e['ou'].'`'.($e['extrait'] !== '' ? ' : '.str_replace('`', "'", $e['extrait']) : '');
                    }
                    if ($r['occurrences'] > count($r['ecarts'])) {
                        $l[] = '- … et '.($r['occurrences'] - count($r['ecarts'])).' autre(s).';
                    }
                    $l[] = '';
                }
                if ($r['note']) {
                    $l[] = $r['note'];
                    $l[] = '';
                }
            }
        }

        $l[] = '## Ce qui n\'a pas été vérifié';
        $l[] = '';
        foreach ($p['hors_de_portee'] ?? [] as $point) {
            $l[] = '- '.$point;
        }
        if (! empty($p['sans_javascript'])) {
            $l[] = '- Le contenu ajouté par JavaScript : les pages sont lues sans exécuter leurs scripts.';
        }
        foreach ($p['ignores'] ?? [] as $raison => $nombre) {
            $l[] = '- '.$nombre.' '.$raison.', ignorés.';
        }
        foreach ($p['non_atteints'] ?? [] as $absent) {
            $l[] = '- Non atteint : '.$absent;
        }
        $l[] = '';
        $l[] = '> Ce bilan constate le respect des règles vérifiables automatiquement, à une date donnée, sur ce qui a été soumis. Il ne vaut ni audit d\'accessibilité ni homologation.';
        $l[] = '';

        return implode("\n", $l);
    }

    private static function cellule(string $texte): string
    {
        return str_replace('|', '\\|', $texte);
    }
}
