<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\DemandeStatus;

class Demande extends Model
{
    use HasFactory;

    protected $fillable = ['montant', 'client_id', 'articles', 'status'];

    protected $casts = [
        'articles' => 'array',
        'status' => 'string' // Laissez le type string et gérez les enums explicitement
    ];

    // public function client()
    // {
    //     return $this->belongsTo(Client::class);
    // }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function setStatusAttribute($value)
    {
        // Assurez-vous que $value est une chaîne de caractères
        if ($value instanceof DemandeStatus) {
            $this->attributes['status'] = $value->value;
        } else {
            $this->attributes['status'] = $value;
        }
    }

    public function getStatusAttribute()
    {
        return DemandeStatus::from($this->attributes['status']);
    }
}
