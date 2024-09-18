<?php

namespace App\Jobs;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\DemandeSubmittedNotification;

class NotifyBoutiquiersAboutDemande implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $demande;

    public function __construct(Demande $demande)
    {
        $this->demande = $demande;
    }

    public function handle()
    {
        $boutiquiers = User::whereHas('roles', function ($query) {
            $query->where('name', 'BOUTIQUIER'); // Assurez-vous que le nom du rôle est correct
        })->get();
        
        Notification::send($boutiquiers, new DemandeSubmittedNotification($this->demande));
    }
}
