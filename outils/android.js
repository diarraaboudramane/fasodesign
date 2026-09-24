#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Projection de tokens.css vers des ressources Android
 *
 *   node outils/android.js              réécrit le dossier android/
 *   node outils/android.js --verifier   n'écrit rien, sort en 1 si le
 *                                       dossier ne correspond plus
 *
 * Le CSS et le JavaScript de la charte ne s'exécutent pas dans une
 * application native. Ce qui se transporte, en revanche, ce sont les
 * décisions : les couleurs officielles, l'échelle d'espacement, les
 * rayons, la typographie, la cible tactile. Cet outil les rend dans
 * les formats qu'Android sait lire, pour que la même décision ne soit
 * pas recopiée à la main dans chaque application, et qu'elle ne
 * dérive pas.
 *
 * Trois conversions méritent d'être expliquées :
 *
 *   rem → sp pour le texte, rem → dp pour les espacements. Le sp
 *   suit la préférence de taille de l'usager, comme le rem suit celle
 *   du navigateur ; le dp ne doit pas la suivre, sous peine de voir
 *   les marges enfler avec le texte.
 *
 *   clamp(min, fluide, max) n'a pas d'équivalent. La borne basse va
 *   dans values/, la borne haute dans values-sw600dp/ : le téléphone
 *   reçoit la petite taille, la tablette la grande.
 *
 *   La cible tactile passe de 44 à 48 dp. La charte impose 44 px sur
 *   le web ; Android en exige 48. On retient la plus stricte des deux.
 *
 * Rien à installer pour l'exécuter.
 */
"use strict";

const fs = require("fs");
const path = require("path");

const RACINE = path.join(__dirname, "..");
const SOURCE = path.join(RACINE, "assets", "css", "tokens.css");
const SORTIE = path.join(RACINE, "android");

const ENTETE =
  "<!--\n" +
  "  Charte graphique de l'administration burkinabè\n" +
  "  Fichier produit par outils/android.js depuis assets/css/tokens.css.\n" +
  "  Ne pas modifier directement : toute retouche serait perdue à la\n" +
  "  prochaine génération. La source faisant foi est la feuille de jetons.\n" +
  "-->\n";

/* ------------------------------------------------------------ lecture */

function blocs(css) {
  function bloc(debut) {
    const i = css.indexOf(debut);
    if (i < 0) throw new Error("bloc introuvable : " + debut);
    const j = css.indexOf("\n}", i);
    return css.slice(i, j).replace(/\/\*[\s\S]*?\*\//g, "");
  }

  function lire(src) {
    const d = {};
    const motif = /(--[\w-]+)\s*:\s*([^;]+);/g;
    let m;
    while ((m = motif.exec(src)) !== null) {
      d[m[1]] = m[2].trim().replace(/\s+/g, " ");
    }
    return d;
  }

  const clair = lire(bloc(":root {"));
  const sombre = Object.assign({}, clair, lire(bloc('[data-theme="dark"]')));
  return { clair, sombre };
}

/** Suit les var() jusqu'à une valeur littérale. */
function resoudre(jetons, valeur, profondeur) {
  if (valeur == null || (profondeur || 0) > 12) return null;
  const m = /^var\((--[\w-]+)\)$/.exec(String(valeur).trim());
  if (m) return resoudre(jetons, jetons[m[1]], (profondeur || 0) + 1);
  return String(valeur).trim();
}

/* ------------------------------------------------------- conversions */

/** #rrggbb ou rgba() vers la notation #aarrggbb d'Android. */
function couleurAndroid(valeur) {
  let m = /^#([0-9a-fA-F]{6})$/.exec(valeur);
  if (m) return "#" + m[1].toUpperCase();

  m = /^rgba?\(([^)]+)\)$/.exec(valeur);
  if (!m) return null;

  const p = m[1].split(",").map((x) => x.trim());
  if (p.length < 3) return null;

  const canal = (x) => Math.max(0, Math.min(255, Math.round(parseFloat(x))));
  const alpha = p.length > 3 ? Math.round(parseFloat(p[3]) * 255) : 255;
  const hex = (n) => n.toString(16).padStart(2, "0").toUpperCase();

  return "#" + hex(alpha) + hex(canal(p[0])) + hex(canal(p[1])) + hex(canal(p[2]));
}

/** rem, px et nombres nus vers dp ou sp. */
function mesure(valeur, unite) {
  let m = /^(-?[\d.]+)rem$/.exec(valeur);
  if (m) return arrondir(parseFloat(m[1]) * 16) + unite;

  m = /^(-?[\d.]+)px$/.exec(valeur);
  if (m) return arrondir(parseFloat(m[1])) + unite;

  m = /^(-?[\d.]+)$/.exec(valeur);
  if (m) return arrondir(parseFloat(m[1])) + unite;

  return null;
}

function arrondir(n) {
  const r = Math.round(n * 100) / 100;
  return String(r);
}

/** clamp(min, …, max) rend ses deux bornes. */
function bornesClamp(valeur) {
  const m = /^clamp\(\s*([^,]+),[^,]+,\s*([^)]+)\)$/.exec(valeur);
  if (!m) return null;
  return { min: m[1].trim(), max: m[2].trim() };
}

function nomAndroid(jeton) {
  return jeton.replace(/^--/, "").replace(/-/g, "_");
}

/* -------------------------------------------------------- production */

function couleurs(jetons, seulementDifferentesDe) {
  const lignes = [];
  for (const cle of Object.keys(jetons)) {
    const brut = resoudre(jetons, jetons[cle]);
    if (!brut) continue;
    const couleur = couleurAndroid(brut);
    if (!couleur) continue;

    if (seulementDifferentesDe) {
      const avant = couleurAndroid(resoudre(seulementDifferentesDe, seulementDifferentesDe[cle]) || "");
      if (avant === couleur) continue;
    }
    lignes.push('  <color name="faso_' + nomAndroid(cle) + '">' + couleur + "</color>");
  }
  return lignes;
}

function dimensions(jetons, borne) {
  const lignes = [];

  const ajouter = (nom, valeur, unite) => {
    const v = mesure(valeur, unite);
    if (v !== null) lignes.push('  <dimen name="faso_' + nom + '">' + v + "</dimen>");
  };

  for (const cle of Object.keys(jetons)) {
    const nom = nomAndroid(cle);
    const brut = resoudre(jetons, jetons[cle]);
    if (!brut) continue;

    const bornes = bornesClamp(brut);
    const valeur = bornes ? bornes[borne] : brut;

    if (/^--taille-/.test(cle)) ajouter(nom, valeur, "sp");
    else if (/^--(espace-|rayon-|trait-|cible-min|largeur-|gouttiere|section-y)/.test(cle)) {
      /* Le rayon « plein » vaut 999 px sur le web : sur Android on le
         laisse au code, qui emploie une forme circulaire. */
      if (cle === "--rayon-plein") continue;
      ajouter(nom, valeur, "dp");
    }
  }

  return lignes;
}

function fichierXml(racine, lignes) {
  return '<?xml version="1.0" encoding="utf-8"?>\n' + ENTETE +
    "<" + racine + ">\n" + lignes.join("\n") + "\n</" + racine + ">\n";
}

/* --------------------------------------------------------- le thème */

function themes() {
  return '<?xml version="1.0" encoding="utf-8"?>\n' + ENTETE +
`<resources>

  <!--
    Thème de base. Les correspondances Material 3 ci-dessous sont le
    seul endroit où une décision de traduction a été prise :

      primary   → action, le vert du drapeau, couleur des commandes
      secondary → relief, l'or, réservé à la mise en avant
      error     → danger, le rouge du drapeau
      outline   → bordure forte, celle qui atteint 3:1

    Le rouge sert d'accent et non de couleur primaire : sur le web
    comme ici, la commande principale est verte.
  -->
  <style name="Theme.Faso" parent="Theme.Material3.DayNight.NoActionBar">

    <item name="colorPrimary">@color/faso_action</item>
    <item name="colorOnPrimary">@color/faso_action_texte</item>
    <item name="colorPrimaryContainer">@color/faso_action_tenue</item>
    <item name="colorOnPrimaryContainer">@color/faso_action_sur_clair</item>

    <item name="colorSecondary">@color/faso_relief</item>
    <item name="colorOnSecondary">@color/faso_relief_texte</item>

    <item name="colorError">@color/faso_danger</item>
    <item name="colorOnError">@color/faso_texte_sur_couleur</item>
    <item name="colorErrorContainer">@color/faso_danger_fond</item>
    <item name="colorOnErrorContainer">@color/faso_danger_texte</item>

    <item name="android:colorBackground">@color/faso_surface</item>
    <item name="colorSurface">@color/faso_surface</item>
    <item name="colorOnSurface">@color/faso_texte</item>
    <item name="colorSurfaceVariant">@color/faso_surface_enfoncee</item>
    <item name="colorOnSurfaceVariant">@color/faso_texte_attenue</item>
    <item name="colorOutline">@color/faso_bordure_forte</item>
    <item name="colorOutlineVariant">@color/faso_bordure</item>

    <item name="android:statusBarColor">@color/faso_surface</item>
    <item name="android:navigationBarColor">@color/faso_surface</item>

    <item name="textAppearanceHeadlineLarge">@style/TextAppearance.Faso.H1</item>
    <item name="textAppearanceHeadlineMedium">@style/TextAppearance.Faso.H2</item>
    <item name="textAppearanceTitleLarge">@style/TextAppearance.Faso.H3</item>
    <item name="textAppearanceBodyLarge">@style/TextAppearance.Faso.Texte</item>
    <item name="textAppearanceBodySmall">@style/TextAppearance.Faso.Legende</item>

    <item name="shapeAppearanceSmallComponent">@style/Forme.Faso.Petite</item>
    <item name="shapeAppearanceMediumComponent">@style/Forme.Faso.Moyenne</item>
    <item name="shapeAppearanceLargeComponent">@style/Forme.Faso.Grande</item>
  </style>

  <!--
    Les polices ne sont pas fournies ici : le paquet ne contient que
    des woff2, que seul un navigateur sait lire. Archivo et Inter sont
    sous licence SIL OFL ; on télécharge les fichiers .ttf depuis la
    même source, on les place dans res/font, et on décosmmente les deux
    lignes fontFamily ci-dessous.
  -->
  <style name="TextAppearance.Faso.H1" parent="TextAppearance.Material3.HeadlineLarge">
    <!-- <item name="fontFamily">@font/archivo</item> -->
    <item name="android:textSize">@dimen/faso_taille_h1</item>
    <item name="android:textColor">@color/faso_texte</item>
    <item name="android:letterSpacing">-0.02</item>
  </style>

  <style name="TextAppearance.Faso.H2" parent="TextAppearance.Material3.HeadlineMedium">
    <!-- <item name="fontFamily">@font/archivo</item> -->
    <item name="android:textSize">@dimen/faso_taille_h2</item>
    <item name="android:textColor">@color/faso_texte</item>
    <item name="android:letterSpacing">-0.02</item>
  </style>

  <style name="TextAppearance.Faso.H3" parent="TextAppearance.Material3.TitleLarge">
    <!-- <item name="fontFamily">@font/archivo</item> -->
    <item name="android:textSize">@dimen/faso_taille_h3</item>
    <item name="android:textColor">@color/faso_texte</item>
  </style>

  <style name="TextAppearance.Faso.Texte" parent="TextAppearance.Material3.BodyLarge">
    <!-- <item name="fontFamily">@font/inter</item> -->
    <item name="android:textSize">@dimen/faso_taille_md</item>
    <item name="android:textColor">@color/faso_texte</item>
  </style>

  <style name="TextAppearance.Faso.Legende" parent="TextAppearance.Material3.BodySmall">
    <!-- <item name="fontFamily">@font/inter</item> -->
    <item name="android:textSize">@dimen/faso_taille_sm</item>
    <item name="android:textColor">@color/faso_texte_faible</item>
  </style>

  <style name="Forme.Faso.Petite" parent="ShapeAppearance.Material3.SmallComponent">
    <item name="cornerSize">@dimen/faso_rayon_sm</item>
  </style>

  <style name="Forme.Faso.Moyenne" parent="ShapeAppearance.Material3.MediumComponent">
    <item name="cornerSize">@dimen/faso_rayon_md</item>
  </style>

  <style name="Forme.Faso.Grande" parent="ShapeAppearance.Material3.LargeComponent">
    <item name="cornerSize">@dimen/faso_rayon_lg</item>
  </style>

  <!--
    Bouton principal. La hauteur minimale reprend la cible tactile :
    48 dp, plancher d'Android, plus strict que les 44 px du web.
  -->
  <style name="Widget.Faso.Bouton" parent="Widget.Material3.Button">
    <item name="android:minHeight">@dimen/faso_cible_min</item>
    <item name="android:minWidth">@dimen/faso_cible_min</item>
    <item name="android:textAppearance">@style/TextAppearance.Faso.Texte</item>
    <item name="cornerRadius">@dimen/faso_rayon_md</item>
    <item name="backgroundTint">@color/faso_action</item>
    <item name="android:textColor">@color/faso_action_texte</item>
  </style>

  <style name="Widget.Faso.Bouton.Secondaire" parent="Widget.Material3.Button.OutlinedButton">
    <item name="android:minHeight">@dimen/faso_cible_min</item>
    <item name="cornerRadius">@dimen/faso_rayon_md</item>
    <item name="strokeColor">@color/faso_action</item>
    <item name="android:textColor">@color/faso_action_sur_clair</item>
  </style>

</resources>
`;
}

/* ------------------------------------------------------- Compose */

function composeCouleurs(clair, sombre) {
  const ligne = (jetons, cle) => {
    const brut = resoudre(jetons, jetons[cle]);
    const c = brut ? couleurAndroid(brut) : null;
    if (!c) return null;
    const hex = c.length === 7 ? "FF" + c.slice(1) : c.slice(1);
    return "0x" + hex;
  };

  const noms = Object.keys(clair).filter((c) => ligne(clair, c));

  const bloc = (jetons, suffixe) => noms.map((c) => {
    const v = ligne(jetons, c);
    return "val " + camel(nomAndroid(c)) + suffixe + " = Color(" + v + ")";
  }).join("\n");

  return `/*
 * Charte graphique de l'administration burkinabè
 * Fichier produit par outils/android.js depuis assets/css/tokens.css.
 * Ne pas modifier directement.
 */
package bf.gouv.charte

import androidx.compose.ui.graphics.Color

/* Palette et rôles, thème clair. */
${bloc(clair, "")}

/* Surcharges du thème sombre. */
${bloc(sombre, "Sombre")}
`;
}

function camel(nom) {
  return nom.replace(/_([a-z0-9])/g, (t, c) => c.toUpperCase());
}

function composeTheme() {
  return `/*
 * Charte graphique de l'administration burkinabè
 * Fichier produit par outils/android.js. Ne pas modifier directement.
 *
 * Les correspondances Material 3 sont les mêmes que celles du thème
 * XML : primary au vert d'action, secondary à l'or de relief, error
 * au rouge. Un service qui n'emploie pas Material peut ignorer ce
 * fichier et lire directement FasoCouleurs.
 */
package bf.gouv.charte

import androidx.compose.foundation.isSystemInDarkTheme
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Shapes
import androidx.compose.material3.Typography
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

private val CouleursClaires = lightColorScheme(
    primary = action,
    onPrimary = actionTexte,
    primaryContainer = actionTenue,
    onPrimaryContainer = actionSurClair,
    secondary = relief,
    onSecondary = reliefTexte,
    error = danger,
    onError = texteSurCouleur,
    errorContainer = dangerFond,
    onErrorContainer = dangerTexte,
    background = surface,
    onBackground = texte,
    surface = surface,
    onSurface = texte,
    surfaceVariant = surfaceEnfoncee,
    onSurfaceVariant = texteAttenue,
    outline = bordureForte,
    outlineVariant = bordure,
)

private val CouleursSombres = darkColorScheme(
    primary = actionSombre,
    onPrimary = actionTexteSombre,
    primaryContainer = actionTenueSombre,
    onPrimaryContainer = actionSurClairSombre,
    secondary = reliefSombre,
    onSecondary = reliefTexteSombre,
    error = dangerSombre,
    onError = texteSurCouleurSombre,
    errorContainer = dangerFondSombre,
    onErrorContainer = dangerTexteSombre,
    background = surfaceSombre,
    onBackground = texteSombre,
    surface = surfaceSombre,
    onSurface = texteSombre,
    surfaceVariant = surfaceEnfonceeSombre,
    onSurfaceVariant = texteAttenueSombre,
    outline = bordureForteSombre,
    outlineVariant = bordureSombre,
)

/*
 * L'échelle typographique du web est fluide entre deux bornes ; ici
 * elle ne l'est pas. Les tailles retenues sont les bornes basses,
 * celles du téléphone. Une application qui vise aussi la tablette
 * peut interpoler d'après windowSizeClass.
 */
private val Typographie = Typography(
    headlineLarge = TextStyle(fontSize = 32.sp, lineHeight = 38.sp,
        fontWeight = FontWeight.W700, letterSpacing = (-0.64).sp),
    headlineMedium = TextStyle(fontSize = 26.sp, lineHeight = 33.sp,
        fontWeight = FontWeight.W700, letterSpacing = (-0.52).sp),
    titleLarge = TextStyle(fontSize = 22.sp, lineHeight = 28.sp,
        fontWeight = FontWeight.W600),
    bodyLarge = TextStyle(fontSize = 15.sp, lineHeight = 24.sp),
    bodySmall = TextStyle(fontSize = 13.sp, lineHeight = 21.sp),
)

private val Formes = Shapes(
    extraSmall = RoundedCornerShape(2.dp),
    small = RoundedCornerShape(4.dp),
    medium = RoundedCornerShape(6.dp),
    large = RoundedCornerShape(10.dp),
    extraLarge = RoundedCornerShape(16.dp),
)

/** Cible tactile minimale : 48 dp, plancher d'Android. */
val cibleMinimale = 48.dp

@Composable
fun FasoTheme(
    sombre: Boolean = isSystemInDarkTheme(),
    contenu: @Composable () -> Unit,
) {
    MaterialTheme(
        colorScheme = if (sombre) CouleursSombres else CouleursClaires,
        typography = Typographie,
        shapes = Formes,
        content = contenu,
    )
}
`;
}

/* --------------------------------------------------------- l'emblème */

function embleme() {
  const svg = fs.readFileSync(path.join(RACINE, "assets", "img", "armoiries.svg"), "utf8");

  const vb = /viewBox="([\d.\s-]+)"/.exec(svg);
  if (!vb) throw new Error("viewBox introuvable dans armoiries.svg");
  const [, , largeur, hauteur] = vb[1].trim().split(/\s+/).map(Number);

  const chemins = [];
  const motif = /<path\b[^>]*>/g;
  let m;
  while ((m = motif.exec(svg)) !== null) {
    const balise = m[0];
    const d = /\sd="([^"]+)"/.exec(balise);
    const fill = /\sfill="(#[0-9a-fA-F]{3,6})"/.exec(balise);
    if (!d) continue;

    /* Android lit la notation scientifique de travers selon les
       versions : on la développe avant d'écrire le fichier. */
    const trace = d[1].replace(/-?\d*\.?\d+e-?\d+/gi,
      (n) => String(Number(n).toFixed(6)).replace(/0+$/, "").replace(/\.$/, ""));

    chemins.push(
      "  <path\n" +
      '      android:fillColor="' + (fill ? fill[1].toUpperCase() : "#000000") + '"\n' +
      '      android:pathData="' + trace + '" />');
  }

  return '<?xml version="1.0" encoding="utf-8"?>\n' + ENTETE +
    "<vector xmlns:android=\"http://schemas.android.com/apk/res/android\"\n" +
    '    android:width="' + Math.round(largeur / 4) + 'dp"\n' +
    '    android:height="' + Math.round(hauteur / 4) + 'dp"\n' +
    '    android:viewportWidth="' + largeur + '"\n' +
    '    android:viewportHeight="' + hauteur + '">\n' +
    chemins.join("\n") + "\n</vector>\n";
}

/* ------------------------------------------------ marques nationales */

/*
 * Le filet et le repere sont les deux marques que porte toute page de
 * la charte. Ils sont ici a l'identique, proportions comprises, et
 * portent tous deux la composition du drapeau : rouge au-dessus, vert
 * au-dessous, etoile d'or centree a cheval sur les deux. Une marque de
 * l'Etat ne se rearrange pas selon le format ou elle se pose.
 *
 * La taille de l'etoile est celle de la feuille de styles : 0,7 de la
 * hauteur pour le filet, 0,42 pour le repere. Le trace de l'etoile est
 * lu dans le jeton --etoile-or, pour qu'une correction du dessin se
 * propage ici sans recopie.
 */
function traceEtoile(jetons) {
  const jeton = jetons["--etoile-or"];
  if (!jeton) throw new Error("jeton --etoile-or introuvable");
  const m = /d='([^']+)'/.exec(decodeURIComponent(jeton));
  if (!m) throw new Error("trace de l'etoile illisible");
  return m[1];
}

function marque(options) {
  const { largeur, hauteur, part, vertical, etoile } = options;

  const taille = hauteur * part;
  const echelle = taille / 100;
  const x = (largeur - taille) / 2;
  const y = (hauteur - 95 * echelle) / 2;
  const n = (v) => String(Math.round(v * 10000) / 10000);

  const moitie = vertical
    ? ['M0,0h' + largeur + 'v' + (hauteur / 2) + 'h-' + largeur + 'z',
       'M0,' + (hauteur / 2) + 'h' + largeur + 'v' + (hauteur / 2) + 'h-' + largeur + 'z']
    : ['M0,0h' + (largeur / 2) + 'v' + hauteur + 'h-' + (largeur / 2) + 'z',
       'M' + (largeur / 2) + ',0h' + (largeur / 2) + 'v' + hauteur + 'h-' + (largeur / 2) + 'z'];

  return '<?xml version="1.0" encoding="utf-8"?>\n' + ENTETE +
    '<vector xmlns:android="http://schemas.android.com/apk/res/android"\n' +
    '    android:width="' + largeur + 'dp"\n' +
    '    android:height="' + hauteur + 'dp"\n' +
    '    android:viewportWidth="' + largeur + '"\n' +
    '    android:viewportHeight="' + hauteur + '">\n' +
    '  <path android:fillColor="#E30613" android:pathData="' + moitie[0] + '" />\n' +
    '  <path android:fillColor="#00843B" android:pathData="' + moitie[1] + '" />\n' +
    '  <group\n' +
    '      android:translateX="' + n(x) + '"\n' +
    '      android:translateY="' + n(y) + '"\n' +
    '      android:scaleX="' + n(echelle) + '"\n' +
    '      android:scaleY="' + n(echelle) + '">\n' +
    '    <path android:fillColor="#FFED00" android:pathData="' + etoile + '" />\n' +
    '  </group>\n' +
    '</vector>\n';
}

/* --------------------------------------------------------- les icones */

/*
 * VectorDrawable ne connaît que <path>. Les cercles et les rectangles
 * du jeu d'icônes sont donc convertis en tracés, avec les mêmes
 * coordonnées : le dessin est identique, c'est son écriture qui change.
 */
function cercleVersTrace(cx, cy, r) {
  return "M" + (cx - r) + "," + cy +
    "a" + r + "," + r + " 0 1,0 " + (2 * r) + ",0" +
    "a" + r + "," + r + " 0 1,0 " + (-2 * r) + ",0z";
}

function rectangleVersTrace(x, y, l, h, rx) {
  if (!rx) {
    return "M" + x + "," + y + "h" + l + "v" + h + "h" + (-l) + "z";
  }
  return "M" + (x + rx) + "," + y +
    "h" + (l - 2 * rx) +
    "a" + rx + "," + rx + " 0 0,1 " + rx + "," + rx +
    "v" + (h - 2 * rx) +
    "a" + rx + "," + rx + " 0 0,1 " + (-rx) + "," + rx +
    "h" + (-(l - 2 * rx)) +
    "a" + rx + "," + rx + " 0 0,1 " + (-rx) + "," + (-rx) +
    "v" + (-(h - 2 * rx)) +
    "a" + rx + "," + rx + " 0 0,1 " + rx + "," + (-rx) + "z";
}

function attribut(balise, nom) {
  const m = new RegExp("\\s" + nom + "='([^']*)'").exec(balise);
  return m ? m[1] : null;
}

function icones() {
  const svg = fs.readFileSync(path.join(RACINE, "assets", "img", "icones.svg"), "utf8");
  const produits = {};

  const motif = /<symbol id='([^']+)'[^>]*>([\s\S]*?)<\/symbol>/g;
  let m;
  while ((m = motif.exec(svg)) !== null) {
    const nom = m[1].replace(/^fs-/, "").replace(/-/g, "_");
    const corps = m[2];
    const titre = (/<title>([^<]*)<\/title>/.exec(corps) || [, ""])[1];
    const traces = [];

    let e;
    const elements = /<(path|circle|rect)\b([^>]*)\/?>/g;
    while ((e = elements.exec(corps)) !== null) {
      const type = e[1];
      const attrs = e[2];
      let d = null;

      if (type === "path") {
        d = attribut(attrs, "d");
      } else if (type === "circle") {
        d = cercleVersTrace(
          Number(attribut(attrs, "cx")),
          Number(attribut(attrs, "cy")),
          Number(attribut(attrs, "r")));
      } else if (type === "rect") {
        d = rectangleVersTrace(
          Number(attribut(attrs, "x")),
          Number(attribut(attrs, "y")),
          Number(attribut(attrs, "width")),
          Number(attribut(attrs, "height")),
          Number(attribut(attrs, "rx") || 0));
      }

      if (d) traces.push(d);
    }

    if (!traces.length) continue;

    const chemins = traces.map((d) =>
      "  <path\n" +
      '      android:pathData="' + d + '"\n' +
      '      android:strokeColor="#FF000000"\n' +
      '      android:strokeWidth="1.5"\n' +
      '      android:strokeLineCap="round"\n' +
      '      android:strokeLineJoin="round" />').join("\n");

    produits["drawable/ic_faso_" + nom + ".xml"] =
      '<?xml version="1.0" encoding="utf-8"?>\n' +
      "<!-- " + titre + " -->\n" + ENTETE +
      '<vector xmlns:android="http://schemas.android.com/apk/res/android"\n' +
      '    android:width="24dp"\n' +
      '    android:height="24dp"\n' +
      '    android:viewportWidth="24"\n' +
      '    android:viewportHeight="24"\n' +
      '    android:tint="?attr/colorOnSurface">\n' +
      chemins + "\n</vector>\n";
  }

  return produits;
}

/* ------------------------------------------------------------ marche */

function produire() {
  const css = fs.readFileSync(SOURCE, "utf8");
  const { clair, sombre } = blocs(css);

  const fichiers = {
    "values/couleurs.xml": fichierXml("resources", couleurs(clair, null)),
    "values-night/couleurs.xml": fichierXml("resources", couleurs(sombre, clair)),
    "values/dimensions.xml": fichierXml("resources", dimensions(clair, "min")),
    "values-sw600dp/dimensions.xml": fichierXml("resources", dimensions(clair, "max")),
    "values/themes.xml": themes(),
    "drawable/faso_armoiries.xml": embleme(),
    "kotlin/FasoCouleurs.kt": composeCouleurs(clair, sombre),
    "kotlin/FasoTheme.kt": composeTheme(),
  };

  const etoile = traceEtoile(clair);
  fichiers["drawable/faso_filet.xml"] =
    marque({ largeur: 100, hauteur: 14, part: 0.7, vertical: true, etoile: etoile });
  fichiers["drawable/faso_repere.xml"] =
    marque({ largeur: 52, hauteur: 12, part: 0.42, vertical: true, etoile: etoile });

  Object.assign(fichiers, icones());

  /* La cible tactile passe de 44 à 48 dp : Android est plus strict
     que le web, et c'est la valeur la plus stricte qui s'applique. */
  for (const cle of ["values/dimensions.xml", "values-sw600dp/dimensions.xml"]) {
    fichiers[cle] = fichiers[cle].replace(
      '<dimen name="faso_cible_min">44dp</dimen>',
      '<!-- 48 dp et non 44 : Android impose un plancher plus haut que le web. -->\n' +
      '  <dimen name="faso_cible_min">48dp</dimen>');
  }

  return fichiers;
}

const fichiers = produire();

if (process.argv.includes("--verifier")) {
  let ecarts = 0;
  for (const [nom, contenu] of Object.entries(fichiers)) {
    const chemin = path.join(SORTIE, nom);
    const actuel = fs.existsSync(chemin) ? fs.readFileSync(chemin, "utf8") : "";
    if (actuel !== contenu) {
      console.error("  android/" + nom + " ne correspond plus a tokens.css");
      ecarts++;
    }
  }
  if (ecarts) {
    console.error("  Relancer : node outils/android.js");
    process.exit(1);
  }
  console.log("  les ressources Android sont conformes a tokens.css");
  process.exit(0);
}

for (const [nom, contenu] of Object.entries(fichiers)) {
  const chemin = path.join(SORTIE, nom);
  fs.mkdirSync(path.dirname(chemin), { recursive: true });
  fs.writeFileSync(chemin, contenu, "utf8");
  const ko = Buffer.byteLength(contenu) / 1024;
  console.log("  android/" + nom.padEnd(34) + ko.toFixed(1).padStart(7) + " Ko");
}
