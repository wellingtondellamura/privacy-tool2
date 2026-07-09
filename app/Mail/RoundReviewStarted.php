<?php

namespace App\Mail;

use App\Models\EvaluationRound;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoundReviewStarted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public EvaluationRound $round,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Processo de Consolidação Iniciado - ' . $this->round->project->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.rounds.review_started',
        );
    }
}
