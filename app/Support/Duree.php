<?php

namespace App\Support;

/**
 * Une durée en minutes, écrite comme on la dit : « 10 minutes »,
 * « 1 heure », « 1 heure 30 minutes », « 24 heures ».
 */
final class Duree
{
    public static function lisible(int $minutes): string
    {
        $heures = intdiv($minutes, 60);
        $reste = $minutes % 60;

        $morceaux = [];
        if ($heures > 0) {
            $morceaux[] = $heures.'&nbsp;heure'.($heures > 1 ? 's' : '');
        }
        if ($reste > 0 || $heures === 0) {
            $morceaux[] = $reste.'&nbsp;minute'.($reste > 1 ? 's' : '');
        }

        return implode(' ', $morceaux);
    }
}
