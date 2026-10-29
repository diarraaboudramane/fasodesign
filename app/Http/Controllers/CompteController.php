<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RegleMotDePasse;

/**
 * Les comptes de l'outil de contrôle de conformité : inscription,
 * connexion, confirmation de l'adresse, mot de passe oublié.
 */
class CompteController extends Controller
{
    private const MESSAGES = [
        'name.required' => 'Indiquez votre nom.',
        'email.required' => 'Indiquez votre adresse électronique.',
        'email.email' => 'Ajoutez le symbole arobase et le nom de domaine, par exemple nom@sante.gov.bf',
        'email.unique' => 'Un compte existe déjà avec cette adresse. Connectez-vous ou demandez un nouveau mot de passe.',
        'entite.required' => 'Indiquez votre ministère, votre institution ou votre société.',
        'password.required' => 'Choisissez un mot de passe.',
        'password.confirmed' => 'Les deux mots de passe saisis ne sont pas identiques.',
        'password.min' => 'Le mot de passe doit comporter au moins :min caractères.',
        '*.max' => 'Ce texte dépasse la longueur autorisée (:max caractères).',
    ];

    /* ------------------------------------------------- inscription */

    public function inscription(): View
    {
        return view('conformite.compte.inscription');
    }

    public function inscrire(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'entite' => ['required', 'string', 'max:190'],
            'password' => ['required', 'confirmed', RegleMotDePasse::min(10)],
        ], self::MESSAGES);

        $compte = new User($donnees);
        $compte->email = Str::lower($compte->email);
        /* Une adresse de l'administration est active dès qu'elle est
           confirmée ; les autres attendent la validation d'un administrateur. */
        $compte->statut = User::adresseAdministration($compte->email) ? 'actif' : 'en_attente';
        $compte->save();

        $compte->sendEmailVerificationNotification();
        Auth::login($compte);
        $request->session()->regenerate();
        Log::info('Compte créé', ['compte' => $compte->id, 'statut' => $compte->statut]);

        return redirect()->route('conformite.courriel');
    }

    /* --------------------------------------------------- connexion */

    public function connexion(): View
    {
        return view('conformite.compte.connexion');
    }

    public function connecter(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], self::MESSAGES);

        $cle = 'connexion|'.Str::lower($donnees['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($cle, 5)) {
            $minutes = (int) ceil(RateLimiter::availableIn($cle) / 60);

            return back()->onlyInput('email')->withErrors([
                'email' => "Trop de tentatives. Réessayez dans {$minutes} minute(s), ou demandez un nouveau mot de passe.",
            ]);
        }

        if (! Auth::attempt(['email' => Str::lower($donnees['email']), 'password' => $donnees['password']], $request->boolean('souvenir'))) {
            RateLimiter::hit($cle, 15 * 60);
            Log::notice('Connexion refusée', ['adresse' => $request->ip()]);

            return back()->onlyInput('email')->withErrors([
                'email' => "L'adresse ou le mot de passe ne correspond à aucun compte.",
            ]);
        }

        RateLimiter::clear($cle);
        $request->session()->regenerate();
        Log::info('Connexion', ['compte' => Auth::id()]);

        return redirect()->intended(route('conformite.espace'));
    }

    public function deconnecter(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('conformite')->with('statut', 'Vous êtes déconnecté.');
    }

    /* ------------------------------------- confirmation de l'adresse */

    public function courriel(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('conformite.espace')
            : view('conformite.compte.courriel');
    }

    public function confirmer(Request $request, int $id, string $empreinte): RedirectResponse
    {
        $compte = $request->user();
        abort_unless($compte->id === $id && hash_equals(sha1($compte->getEmailForVerification()), $empreinte), 403);

        if (! $compte->hasVerifiedEmail()) {
            $compte->markEmailAsVerified();
        }

        return redirect()->route('conformite.espace')->with('statut', 'Votre adresse est confirmée.');
    }

    public function renvoyer(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return back()->with('statut', 'Un nouveau lien de confirmation vient de vous être envoyé.');
    }

    /* ---------------------------------------------- mot de passe oublié */

    public function oubli(): View
    {
        return view('conformite.compte.oubli');
    }

    public function envoyerLien(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']], self::MESSAGES);
        Password::sendResetLink(['email' => Str::lower($request->input('email'))]);

        /* Même réponse que l'adresse existe ou non : rien n'indique à un
           tiers qui a un compte. */
        return back()->with('statut', 'Si un compte existe avec cette adresse, un lien de réinitialisation vient d\'y être envoyé. Il est valable une heure.');
    }

    public function reinitialisation(Request $request, string $jeton): View
    {
        return view('conformite.compte.reinitialisation', ['jeton' => $jeton, 'email' => $request->query('email')]);
    }

    public function reinitialiser(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', RegleMotDePasse::min(10)],
        ], self::MESSAGES);

        $statut = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $compte, string $motDePasse) {
                $compte->forceFill(['password' => $motDePasse, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($compte));
            }
        );

        return $statut === Password::PASSWORD_RESET
            ? redirect()->route('connexion')->with('statut', 'Votre mot de passe est changé. Vous pouvez vous connecter.')
            : back()->onlyInput('email')->withErrors(['email' => 'Ce lien n\'est plus valable. Demandez-en un nouveau.']);
    }
}
