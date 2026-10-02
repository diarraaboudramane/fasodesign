/**
 * Charte graphique de l'administration burkinabè
 * Point d'entrée à importer depuis votre application.
 *
 * L'ordre des feuilles compte : les jetons d'abord, la feuille du
 * projet en dernier. Les trois sont obligatoires et solidaires,
 * plusieurs composants puisant leurs repères dans icones.css.
 */
import '@govbf/fasodesign/css/tokens.css';
import '@govbf/fasodesign/css/faso.css';
import '@govbf/fasodesign/css/icones.css';

import Faso from '@govbf/fasodesign';

/* Une seule ligne, au démarrage. L'observation prend en charge tout
   ce que le cadriciel rendra ensuite : il n'y a plus rien à appeler
   après chaque rendu, et rien à défaire au démontage. */
export function amorcerCharte() {
  Faso.observer();
}

export default Faso;
