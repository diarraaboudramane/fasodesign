/*
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
