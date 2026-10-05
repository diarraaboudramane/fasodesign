<?php

namespace App\Http\Controllers;

use App\Mail\MessageContact;
use App\Rules\Recaptcha;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function afficher(): View
    {
        return view('contact', ['cleSite' => config('services.recaptcha.site_key')]);
    }

    public function envoyer(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:120'],
            'courriel' => ['required', 'email', 'max:190'],
            'objet' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:4000'],
            'g-recaptcha-response' => [new Recaptcha($request->ip())],
        ], [
            'nom.required' => 'Indiquez votre nom.',
            'courriel.required' => 'Indiquez votre adresse électronique.',
            'courriel.email' => 'Ajoutez le symbole arobase et le nom de domaine, par exemple nom@exemple.bf',
            'objet.required' => "Indiquez l'objet de votre message.",
            'message.required' => 'Écrivez votre message.',
            '*.max' => 'Ce texte dépasse la longueur autorisée (:max caractères).',
        ]);

        $destinataire = config('charte.contact.destinataire');
        if (blank($destinataire)) {
            Log::error('Formulaire de contact : CONTACT_DESTINATAIRE absent, message non transmis.');

            return back()->withInput()->withErrors([
                'envoi' => "Le formulaire n'est pas encore en service. Votre message n'a pas été transmis.",
            ]);
        }

        unset($donnees['g-recaptcha-response']);
        Mail::to($destinataire)->send(new MessageContact($donnees));

        return redirect()->route('contact')->with('envoye', true);
    }
}
