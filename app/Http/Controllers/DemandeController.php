<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Services\DemandeService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DemandeRequest;
use App\Jobs\NotifyBoutiquiersAboutDemande;

class DemandeController extends Controller
{
    protected $demandeService;

    public function __construct(DemandeService $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    public function create(DemandeRequest $request)
    {
        $user = Auth::user();
        if (!$user || !$user->client) {
            return response()->json(['error' => 'Utilisateur non authentifié ou client non associé'], 401);
        }

        $validatedData = $request->validated();

        try {
            // Crée la demande en passant le client authentifié et les données validées
            $demande = $this->demandeService->createDemande($validatedData, $user->client);

            // Envoie une notification aux boutiquiers
            NotifyBoutiquiersAboutDemande::dispatch($demande);
            return response()->json($demande, 201);
        } catch (\Exception $e) {
            // Renvoie une erreur si la validation échoue
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getByClient()
    {
        $user = Auth::user(); // Obtient l'utilisateur authentifié

        if (!$user || !$user->client) {
            return response()->json(['error' => 'Utilisateur non authentifié ou client non associé'], 401);
        }

        $demandes = $this->demandeService->getDemandesByClientId($user->client->id);

        return response()->json($demandes);
    }

    public function getAll()
    {
        $demandes = $this->demandeService->getAllDemandes();
        return response()->json($demandes);
    }

    public function validateDemande($id)
    {
        $demande = $this->demandeService->getDemandeById($id);

        try {
            $this->demandeService->validateDemande($demande);
            $this->demandeService->markAsValidated($demande);
            return response()->json(['message' => 'Demande validée et convertie en dette.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelDemande($id)
    {
        $demande = $this->demandeService->getDemandeById($id);

        $this->demandeService->cancelDemande($demande);
        return response()->json(['message' => 'Demande annulée.'], 200);
    }
}
