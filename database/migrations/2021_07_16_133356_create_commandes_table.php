<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->date('date_livraison')->default(null);
            $table->string('numero_bon_commande')->default(null);
            $table->string('quantité'); 
            $table->foreignId('fournisseur_id')->constrained('fournisseurs')->default(null);
            $table->foreignId('fourniture_id')->constrained('fournitures');
            $table->foreignId('commandes_fichier_id')->constrained('commandes_fichiers')->default(null);
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
        Schema::dropIfExists('commandes');
    }
}
