<?php

namespace App\Repositories;

use App\Models\Demande;

class DemandeRepository implements DemandeRepositoryInterface
{
    public function create(array $data)
    {
        return Demande::create($data);
    }

    public function findById($id)
    {
        return Demande::find($id);
    }

    public function getAll()
    {
        return Demande::all();
    }

    public function getByClientId($clientId)
    {
        return Demande::where('client_id', $clientId)->get();
    }
}
