<?php

namespace App\Services;

use App\Models\Dette;
use App\Models\Client;
use App\Models\Demande;
use App\Models\Article;
use App\Enums\DemandeStatus;
use Illuminate\Support\Facades\Log;
use App\Repositories\DemandeRepositoryInterface;

class DemandeService
{
    protected $demandeRepository;

    public function __construct(DemandeRepositoryInterface $demandeRepository)
    {
        $this->demandeRepository = $demandeRepository;
    }

    public function createDemande(array $data, Client $client)
    {
        $this->validateClientCategory($client, $data['montant']);

        $data['client_id'] = $client->id;
        $data['status'] = DemandeStatus::EN_COURS; // Utilisation de l'objet enum

        return $this->demandeRepository->create($data);
    }

    public function cancelDemande(Demande $demande)
    {
        if ($demande->status === DemandeStatus::VALIDER) {
            throw new \Exception("Une demande validée ne peut pas être annulée.");
        }

        $demande->status = DemandeStatus::EN_COURS; // Utilisation de l'objet enum
        $demande->save();

        Log::info("Demande annulée.", [
            'demande_id' => $demande->id,
            'client_id' => $demande->client_id
        ]);
    }


    private function validateClientCategory(Client $client, float $montant)
    {
        $totalDettes = $client->demandes()->whereIn('status', [
            DemandeStatus::EN_COURS->value,
            DemandeStatus::ANNULER->value
        ])->sum('montant');

        Log::info("Validation de la catégorie du client:", [
            'category' => $client->category->libelle,
            'montant' => $montant,
            'totalDettes' => $totalDettes,
            'max_montant' => $client->max_montant
        ]);

        switch ($client->category->libelle) {
            case 'Gold':
                // Pas de restriction pour les clients Gold
                break;
            case 'Silver':
                if ($client->max_montant && ($totalDettes + $montant) > $client->max_montant) {
                    throw new \Exception("Le montant total des dettes (y compris la nouvelle demande) dépasse le montant maximum autorisé pour ce client Silver.");
                }
                break;
            case 'Bronze':
                if ($client->demandes()->whereIn('status', [
                    DemandeStatus::EN_COURS->value,
                    DemandeStatus::ANNULER->value
                ])->exists()) {
                    throw new \Exception("Les clients Bronze ne peuvent pas faire de nouvelle demande s'ils ont des dettes en cours.");
                }
                break;
            default:
                throw new \Exception("Catégorie de client non reconnue.");
        }
    }

    public function markAsValidated(Demande $demande)
    {
        // Comparaison avec l'énumération
        if ($demande->status !== DemandeStatus::EN_COURS) {
            throw new \Exception("La demande doit être en cours pour pouvoir être validée.");
        }

        // Mettre à jour le statut de la demande
        $demande->status = DemandeStatus::VALIDER->value; // Assurez-vous d'utiliser la valeur de l'énumération
        $demande->save();

        // Créer une dette à partir de la demande validée
        $this->createDetteFromDemande($demande);

        Log::info("Demande validée et convertie en dette.", [
            'demande_id' => $demande->id,
            'client_id' => $demande->client_id,
            'montant' => $demande->montant
        ]);
    }

    protected function createDetteFromDemande(Demande $demande)
    {
        // Créer une nouvelle dette avec les détails de la demande validée
        $dette = new Dette();
        $dette->client_id = $demande->client_id;
        $dette->montant = $demande->montant;

        // Sauvegarder la dette
        $dette->save();

        // Récupérer les articles associés à la demande et les ajouter à la dette
        $articles = json_decode($demande->articles, true); // Assurez-vous que les articles sont décodés correctement
        foreach ($articles as $articleData) {
            // Vous pouvez ajuster cette partie selon votre structure d'articles
            $article = Article::find($articleData['id']); // Assurez-vous que vous récupérez l'article correctement

            if ($article) {
                // Créer une relation ou une entrée associée pour chaque article
                $dette->articles()->attach($article->id, [
                    'quantite' => $articleData['quantite'], // Adaptez cette partie selon vos besoins
                    'prix' => $articleData['prix'] // Adaptez cette partie selon vos besoins
                ]);
            }
        }
    }

    public function getDemandesByClientId($clientId)
    {
        return $this->demandeRepository->getByClientId($clientId);
    }

    public function getDemandeById($id)
    {
        return $this->demandeRepository->findById($id);
    }

    public function getAllDemandes()
    {
        return $this->demandeRepository->getAll();
    }

    public function validateDemande(Demande $demande)
    {
        $demande->status = DemandeStatus::VALIDER;
        $demande->save();
    }
}
