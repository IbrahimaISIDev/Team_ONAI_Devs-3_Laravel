<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'montant', 'date_echeance'];
    protected $dates = ['date_echeance'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function getMontantRestantAttribute()
    {
        return $this->montant - $this->paiements->sum('montant');
    }

    public function getMontantDuAttribute()
    {
        return $this->montant;
    }

    // public function articles()
    // {
    //     return $this->hasMany(Article::class);  // Relation avec les articles liés à la dette
    // }

    // Relation many-to-many avec les articles via une table pivot
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
                    ->withPivot('quantite', 'prix'); // Adaptez les colonnes du pivot selon vos besoins
    }
}
