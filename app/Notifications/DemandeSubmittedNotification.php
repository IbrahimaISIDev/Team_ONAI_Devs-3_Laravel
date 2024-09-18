<?php

namespace App\Notifications;

use App\Models\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DemandeSubmittedNotification extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable)
    {
        return ['mail']; // Vous pouvez ajouter d'autres canaux comme 'database' si nécessaire
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle Demande Soumise')
            ->line('Une nouvelle demande a été soumise.')
            ->action('Voir la demande', url('/demandes/' . $this->demande->id))
            ->line('Merci d\'utiliser notre application!');
    }
}
