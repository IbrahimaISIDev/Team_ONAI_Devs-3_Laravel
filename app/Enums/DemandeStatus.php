<?php

namespace App\Enums;

enum DemandeStatus: string
{
    case ANNULER = 'ANNULER';
    case EN_COURS = 'EN COURS';
    case VALIDER = 'VALIDER';
}
