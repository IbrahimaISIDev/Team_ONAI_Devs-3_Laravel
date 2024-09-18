<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemandeRequest extends FormRequest
{
    // Autorise tous les utilisateurs à soumettre cette requête
    public function authorize()
    {
        return true;
    }

    // Définir les règles de validation
    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'articles' => 'required|array',
            'articles.*.article_id' => 'required|integer|exists:articles,id',
            'articles.*.prix' => 'required|numeric|min:0',
            'articles.*.quantite' => 'required|integer|min:1',
        ];
    }

    // Personnaliser les messages d'erreur si nécessaire
    public function messages()
    {
        return [
            'articles.*.article_id.exists' => 'L\'article sélectionné n\'existe pas.',
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'articles.required' => 'La demande doit contenir au moins un article.'
        ];
    }
}

