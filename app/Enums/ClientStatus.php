<?php

namespace App\Enums;

enum ClientStatus: string
{
    case BRONZE = 'BRONZE';
    case SILVER = 'SILVER';
    case GOLD = 'GOLD';
}