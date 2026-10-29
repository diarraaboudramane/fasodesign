<?php

namespace App\Conformite;

use App\Conformite\Regles;
use Throwable;

/**
 * Applique les règles à un corpus et en tire le bilan.
 *
 * Niveau global : « conforme » sans écart bloquant ni majeur, « réserves »
 * sans écart bloquant, « non conforme » sinon. Pas de note chiffrée : elle
 * pousserait à faire monter un nombre plutôt qu'à corriger ce qui compte.
 */
final class Moteur
{
    /** Ce qu'une machine ne sait pas juger, et que chaque bilan rappelle. */
    public const HORS_DE_PORTEE = [
        'La clarté des textes, des messages d\'erreur et le ton administratif.',
        'L\'usage réel au clavier et avec un lecteur d\'écran.',
        'Les proportions de l\'emblème quand il est inclus dans une image.',
        'La cohérence du parcours au-delà de la présence des blocs attendus.',
        'Les contrastes de couleur réellement affichés.',
    ];

    /** @return list<Regle> */
    public static function regles(): array
    {
        return [
            new Regles\FeuillesChargees,
            new Regles\VersionAJour,
            new Regles\FichiersNonModifies,
            new Regles\BlocMarque,
            new Regles\BandeauOfficiel,
            new Regles\CouleursEnDur,
            new Regles\TeintesAnciennes,
            new Regles\ClassesExistantes,
            new Regles\ClassesNonSurchargees,
            new Regles\LangueDeclaree,
            new Regles\TextesDeRemplacement,
            new Regles\HierarchieTitres,
            new Regles\EtiquettesDesChamps,
            new Regles\LibellesExplicites,
            new Regles\DomaineOfficiel,
            new Regles\RessourcesTierces,
            new Regles\BudgetDePoids,
            new Regles\ContrastesAffiches,
        ];
    }

    /**
     * @return array{niveau: string, bloquants: int, majeurs: int, mineurs: int, resultats: list<array>, perimetre: array}
     */
    public function analyser(Corpus $corpus): array
    {
        $resultats = [];
        $comptes = ['bloquante' => 0, 'majeure' => 0, 'mineure' => 0];

        foreach (self::regles() as $regle) {
            if (! in_array($corpus->mode, $regle->modes(), true)) {
                $verdict = ['statut' => 'non_verifiable', 'ecarts' => [], 'note' => 'Ne se vérifie que sur un site en ligne.'];
            } else {
                try {
                    $verdict = $regle->verifier($corpus);
                } catch (Throwable $e) {
                    report($e);
                    $verdict = ['statut' => 'non_verifiable', 'ecarts' => [], 'note' => 'La règle n\'a pas pu être appliquée.'];
                }
            }

            $total = count($verdict['ecarts']);
            if ($verdict['statut'] === 'non_respectee') {
                $comptes[$regle->gravite()]++;
            }

            $resultats[] = [
                'id' => $regle->id(),
                'titre' => $regle->titre(),
                'famille' => $regle->famille(),
                'gravite' => $regle->gravite(),
                'lien' => $regle->lien(),
                'correction' => $regle->correction(),
                'statut' => $verdict['statut'],
                'note' => $verdict['note'] ?? null,
                'occurrences' => $total,
                'ecarts' => array_slice($verdict['ecarts'], 0, Regle::ECARTS_GARDES),
            ];
        }

        $niveau = match (true) {
            $comptes['bloquante'] > 0 => 'non_conforme',
            $comptes['majeure'] > 0 => 'reserves',
            default => 'conforme',
        };

        Balisage::oublier();

        return [
            'niveau' => $niveau,
            'bloquants' => $comptes['bloquante'],
            'majeurs' => $comptes['majeure'],
            'mineurs' => $comptes['mineure'],
            'resultats' => $resultats,
            'perimetre' => [
                'pages' => count($corpus->documents('html')),
                'fichiers' => count($corpus->documents()),
                'version_declaree' => $corpus->versionDeclaree,
                'non_atteints' => array_slice($corpus->nonAtteints, 0, 50),
                'ignores' => $corpus->ignores,
                /* Avec le rendu, les contrastes affichés sont mesurés : ils
                   sortent de ce qui n'est pas vérifié. */
                'hors_de_portee' => $corpus->rendu
                    ? array_values(array_diff(self::HORS_DE_PORTEE, [self::HORS_DE_PORTEE[4]]))
                    : self::HORS_DE_PORTEE,
                'sans_javascript' => $corpus->mode === 'adresse' && ! $corpus->rendu,
                'rendu' => $corpus->rendu,
            ],
        ];
    }
}
