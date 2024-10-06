<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixArticlesColumnConversion extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Assurez-vous que la colonne 'articles' est bien définie comme JSON
            DB::statement('ALTER TABLE demandes ALTER COLUMN articles TYPE json USING articles::json');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            // Revenir à l'état précédent si nécessaire
            // Notez que le type 'text' est utilisé comme exemple. Assurez-vous de spécifier le type précédent.
            DB::statement('ALTER TABLE demandes ALTER COLUMN articles TYPE text USING articles::text');
        });
    }
}

