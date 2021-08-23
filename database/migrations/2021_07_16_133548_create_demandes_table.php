<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->string('référence');
            $table->date('date');
            $table->integer('quantité');
            $table->integer('quantité_demandé');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->foreignId('fourniture_id')->constrained('fournitures');
            $table->foreignId('demandes_fichier_id')->constrained('demandes_fichiers')->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Demandes');
    }
}
