<?php

namespace App\Mail;

use App\Models\Analyse;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BilanPret extends Mailable
{
    public function __construct(public readonly Analyse $analyse) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Contrôle de conformité — '.$this->analyse->libelleNiveau().' — '.$this->analyse->cible);
    }

    public function content(): Content
    {
        return new Content(text: 'mail.bilan');
    }
}
