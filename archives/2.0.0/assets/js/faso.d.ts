/**
 * Charte graphique de l'administration burkinabè
 * Déclarations de types pour faso.js
 *
 * Le fichier JavaScript est volontairement sans dépendance et sans
 * étape de compilation. Ces déclarations existent pour que les
 * projets en TypeScript — Angular en particulier — puissent
 * l'appeler en mode strict.
 */

/** Registre d'un message passager. */
export interface OptionsNotification {
  /** Première ligne, en gras. Facultative. */
  titre?: string;
  /** Corps du message. */
  texte?: string;
  /** Registre visuel. Sans valeur, le message est neutre. */
  ton?: "succes" | "alerte" | "danger";
  /**
   * Durée d'affichage en millisecondes. 6000 par défaut.
   * Une valeur nulle ou négative laisse le message jusqu'à
   * fermeture par l'usager.
   */
  duree?: number;
}

/**
 * Annonces émises par la charte.
 *
 * Chacune porte deux noms : `fs:<nom>` et `fs-<nom>`. Les deux
 * transportent le même détail et sont émises l'une après l'autre.
 * La forme à trait d'union existe parce qu'un gabarit d'Angular ne
 * peut pas lier un événement dont le nom contient deux-points :
 * `(fs:onglet)` y désignerait la cible « fs ».
 *
 * Les annonces annulables précèdent une écriture d'état.
 * `preventDefault()` empêche la charte d'écrire l'attribut, sans
 * rien lui retirer de ce qu'elle assure par ailleurs : clavier,
 * focus, ordre de tabulation, annonce vocale.
 */
export interface AnnoncesFaso {
  /** Avant `aria-selected`, `hidden` et `tabindex` d'un jeu d'onglets. Annulable. */
  "fs:onglet": CustomEvent<{ onglet: HTMLElement; index: number }>;
  /** Avant `data-ouvert` et `aria-expanded` d'un élément repliable. Annulable. */
  "fs:bascule": CustomEvent<{ ouvert: boolean }>;
  /** Avant la largeur et `aria-valuenow` d'une jauge. Annulable. */
  "fs:jauge": CustomEvent<{ valeur: number }>;
  /** Avant la largeur d'une barre d'un groupe. Annulable. */
  "fs:barre": CustomEvent<{ valeur: number; max: number }>;
  /** Avant `data-lu` sur une notification. Annulable. */
  "fs:lecture": CustomEvent<{ tout: boolean }>;
  /** Avant le retrait d'un encart du document. Annulable. */
  "fs:fermeture": CustomEvent<null>;
  /**
   * Après un tri ou un changement de filtre. Informe seulement.
   * `rejeu` distingue un re-rendu du cadriciel d'un changement
   * demandé par l'usager.
   */
  "fs:donnees": CustomEvent<{
    annoncer: boolean; rejeu: boolean; retenues: number;
  } | null>;
}

/**
 * Libellés produits par le script, et langue de mise en forme des
 * nombres et des dates.
 *
 * L'objet exposé par `Faso.textes` est celui que la bibliothèque lit :
 * on le complète, on ne le remplace pas.
 *
 *     Object.assign(Faso.textes, { pageSuivante: '…' });
 *
 * Les accolades délimitent les valeurs à insérer : `{n}`, `{page}`,
 * `{pages}`, `{debut}`, `{fin}`, `{total}`, `{date}`, `{filtres}`,
 * `{fichier}`, `{presentes}`. La marque `{s}` porte le pluriel.
 */
export interface TextesFaso {
  /** Étiquette BCP 47 passée à `toLocaleString`. « fr-FR » par défaut. */
  langue: string;
  /** Les douze mois, en minuscules, pour la date d'extraction. */
  mois: string[];
  [cle: string]: string | string[];
}

/**
 * Décision de consentement : une clé par finalité déclarée dans le
 * balisage par `data-finalite`, plus la date d'enregistrement.
 */
export interface DecisionConsentement {
  /** Jour de l'enregistrement, au format AAAA-MM-JJ. */
  date: string;
  [finalite: string]: boolean | string;
}

export interface Faso {
  /** Version du système, alignée sur celle du paquet. */
  readonly version: string;

  /**
   * Initialise les comportements liés élément par élément.
   *
   * Sans argument, parcourt le document entier. Avec un élément,
   * se limite à son sous-arbre — c'est la forme à employer depuis
   * un cadriciel, après le rendu d'un composant.
   *
   * L'opération est idempotente : un élément déjà initialisé est
   * ignoré, si bien qu'un appel répété n'empile pas les
   * gestionnaires. Sans DOM — rendu côté serveur — l'appel est
   * sans effet.
   */
  initialiser(racine?: ParentNode): void;

  /**
   * Active l'initialisation automatique des sous-arbres ajoutés
   * au document. Une fois appelée, plus rien n'est à faire après
   * chaque rendu.
   *
   * `observer(false)` la désactive. Renvoie l'état effectif :
   * `false` hors navigateur.
   */
  observer(actif?: boolean): boolean;

  /**
   * Affiche un message passager. Renvoie une fonction qui le
   * retire avant l'échéance.
   */
  notifier(options: OptionsNotification): () => void;

  /**
   * Compose un CSV à partir de données, et non du document.
   *
   * C'est la voie à suivre lorsque le cadriciel possède les
   * lignes : il fournit l'intégralité du jeu, la charte se charge
   * de l'échappement, du séparateur et des fins de ligne.
   */
  versCsv(entetes: string[], lignes: Array<Array<string | number>>): string;

  /**
   * Déclenche le téléchargement d'un CSV, avec l'indicateur
   * d'ordre des octets qu'attendent les tableurs.
   */
  telechargerCsv(nom: string, contenu: string): void;

  /**
   * Force un thème. `"dark"` ou `"light"` ; toute valeur vide
   * rétablit le thème clair, qui est le défaut du système.
   */
  appliquerTheme(valeur?: "dark" | "light" | null): void;

  /**
   * Libellés produits par le script. À compléter avant
   * l'initialisation, jamais à remplacer.
   */
  readonly textes: TextesFaso;

  /**
   * Consentement au dépôt de traceurs.
   *
   * La charte recueille la décision, la conserve six mois dans un
   * cookie et la restitue. Elle ne dépose ni ne retire aucun traceur :
   * cela appartient au service, qui écoute `fs:consentement`.
   */
  readonly consentement: {
    /**
     * La décision, ou `null` tant que l'usager n'a pas répondu.
     * `null` n'est pas un refus : c'est une absence de réponse, et
     * aucun traceur non essentiel ne doit être déposé dans cet état.
     */
    lire(): DecisionConsentement | null;
    /** Enregistre une décision et prévient le service. */
    definir(finalites: Record<string, boolean>): DecisionConsentement;
    /** Efface la décision : la question sera reposée. */
    oublier(): void;
  };

  /**
   * Comportements pris isolément. Exposés pour la documentation
   * et les tests ; un projet n'a normalement pas à les appeler.
   */
  readonly comportements: Readonly<Record<string, (racine?: ParentNode) => void>>;
}

declare const Faso: Faso;
export default Faso;

declare global {
  interface Window {
    /** Présent dès le chargement de faso.js dans un navigateur. */
    Faso: Faso;

    /**
     * Posé à `true` avant le chargement, empêche l'initialisation
     * automatique. L'application appelle alors `initialiser()` ou
     * `observer()` au moment qu'elle choisit — ce que réclame un
     * rendu côté serveur avec hydratation.
     */
    FASO_SANS_DEMARRAGE?: boolean;
  }
}
