<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToDemandesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('demandes', 'client_id')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')->after('id');

                // Ajout de la clé étrangère
                $table->foreign('client_id')->references('id')->on('clients');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('demandes', 'client_id')) {
            Schema::table('demandes', function (Blueprint $table) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            });
        }
    }
}


