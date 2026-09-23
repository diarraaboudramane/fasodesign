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
   * Force un thème. `"dark"` ou `"light"` ; toute valeur vide
   * rétablit le thème clair, qui est le défaut du système.
   */
  appliquerTheme(valeur?: "dark" | "light" | null): void;

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
  }
}
