<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\PaiementController;
use App\Jobs\ArchiveDettesPayees;
use App\Jobs\EnvoyerRecapitulatifHebdomadaire;

/**
 * Routes publiques (aucune authentification requise)
 */
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

/**
 * Routes protégées (auth:api, blacklisted)
 */
Route::middleware(['auth:api', 'blacklisted'])->prefix('v1')->group(function () {

     /**
     * Gestion des demandes
     */
    Route::prefix('demandes')->group(function () {
        Route::post('/', [DemandeController::class, 'create']);
        Route::get('/', [DemandeController::class, 'getByClient']);
        Route::get('/all', [DemandeController::class, 'getAll']);
        Route::patch('/{id}/validate', [DemandeController::class, 'validateDemande']);
        Route::patch('/{id}/cancel', [DemandeController::class, 'cancelDemande']);
    });

    /**
     * Authentification et utilisateur
     */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('/users', UserController::class);

    /**
     * Gestion des clients
     */
    Route::prefix('clients')->group(function () {
        Route::post('/telephone', [ClientController::class, 'getByPhoneNumber']);
        Route::post('/register', [ClientController::class, 'addAccount']);
        Route::get('/{id}/user', [ClientController::class, 'getClientUser']);
        Route::post('/{clientId}/add-account', [ClientController::class, 'addAccount']);
        Route::get('/{client}/dettes', [DetteController::class, 'clientDettes']);
    });
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);

    /**
     * Gestion des dettes
     */
    Route::prefix('dettes')->group(function () {
        Route::apiResource('/', DetteController::class);
        Route::post('/{dette}/paiements', [DetteController::class, 'addPaiement']);
        Route::get('/{dette}', [DetteController::class, 'show']);
        Route::get('/filter', [DetteController::class, 'index']);
        Route::get('/search', [DetteController::class, 'index']);
        Route::get('/filter/client/{clientId}', [DetteController::class, 'filterByClient']);
        Route::get('/filter/date-range', [DetteController::class, 'filterByDateRange']);
        Route::get('/search/description', [DetteController::class, 'searchByDescription']);
        Route::get('/filter/solde', [DetteController::class, 'filterBySolde']);
        Route::get('/advanced-search', [DetteController::class, 'advancedSearch']);
    });

    /**
     * Gestion des paiements
     */
    Route::post('/dettes/{dette}/paiements', [PaiementController::class, 'store']);

    /**
     * Archivage des dettes
     */
    Route::prefix('archive')->group(function () {
        Route::post('/dettes', [ArchiveController::class, 'archiveDettes']);
        Route::get('/dettes', [ArchiveController::class, 'showArchivedDettes']);
        Route::get('/dettes/{detteId}', [ArchiveController::class, 'showArchivedDetails']);
        Route::get('/clients/{clientId}/dettes', [ArchiveController::class, 'showClientArchivedDettes']);
    });

    /**
     * Restauration des dettes
     */
    Route::prefix('restaure')->group(function () {
        Route::get('/', [ArchiveController::class, 'getRestorableDettes']);
        Route::patch('/dette/{detteId}', [ArchiveController::class, 'restoreDette']);
        Route::patch('/client/{clientId}', [ArchiveController::class, 'restoreClientDettes']);
    });

    /**
     * Gestion des articles
     */
    Route::prefix('articles')->group(function () {
        Route::apiResource('/', ArticleController::class);
        Route::post('/trashed', [ArticleController::class, 'trashed']);
        Route::patch('/{id}/restore', [ArticleController::class, 'restore']);
        Route::post('/libelle', [ArticleController::class, 'getByLibelle']);
        Route::delete('/{id}/force-delete', [ArticleController::class, 'forceDelete']);
        Route::post('/stock', [ArticleController::class, 'updateMultiple']);
    });

    /**
     * Tâches de fond (Jobs)
     */
    Route::prefix('jobs')->group(function () {
        Route::post('/archive', function () {
            ArchiveDettesPayees::dispatch();
            return response()->json(['message' => 'Archivage déclenché'], 200);
        });
        Route::post('/recap', function () {
            EnvoyerRecapitulatifHebdomadaire::dispatch();
            return response()->json(['message' => 'Envoi du récapitulatif déclenché'], 200);
        });
    });
});
