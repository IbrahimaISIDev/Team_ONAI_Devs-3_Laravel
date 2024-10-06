<?php

namespace App\Repositories;

interface DemandeRepositoryInterface
{
    public function create(array $data);
    public function findById($id);
    public function getAll();
    public function getByClientId($clientId);
}
