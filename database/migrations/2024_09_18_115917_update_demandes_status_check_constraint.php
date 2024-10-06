<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDemandesStatusCheckConstraint extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Pour PostgreSQL, il est plus simple de supprimer la contrainte et la recréer
            DB::statement('ALTER TABLE demandes DROP CONSTRAINT IF EXISTS demandes_status_check');
            
            // Ajoutez la contrainte mise à jour avec les nouvelles valeurs
            DB::statement('ALTER TABLE demandes ADD CONSTRAINT demandes_status_check CHECK (status IN (\'EN COURS\', \'ANNULER\', \'VALIDER\'))');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Revenir à l'état précédent si nécessaire
            DB::statement('ALTER TABLE demandes DROP CONSTRAINT IF EXISTS demandes_status_check');

            // Recréer la contrainte précédente
            DB::statement('ALTER TABLE demandes ADD CONSTRAINT demandes_status_check CHECK (status IN (\'EN COURS\', \'ANNULER\'))');
        });
    }
}
