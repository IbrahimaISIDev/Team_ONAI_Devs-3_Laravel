<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Utilisation de la commande SQL pour convertir la colonne 'articles' en type JSON
            DB::statement('ALTER TABLE demandes ALTER COLUMN articles TYPE json USING articles::json');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Revenir à l'état précédent si nécessaire
            DB::statement('ALTER TABLE demandes ALTER COLUMN articles TYPE text USING articles::text');
        });
    }
}
