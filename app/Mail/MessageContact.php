<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MessageContact extends Mailable
{
    /** @param  array{nom: string, courriel: string, objet: string, message: string}  $donnees */
    public function __construct(public readonly array $donnees) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->donnees['courriel'], $this->donnees['nom'])],
            subject: '[Charte graphique] '.$this->donnees['objet'],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'mail.contact');
    }
}
