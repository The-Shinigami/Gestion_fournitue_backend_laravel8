<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Commandes_fichier;

class Commande extends Model
{
  use HasFactory;
  protected $fillable =['date_livraison','numero_bon_commande','quantité','fournisseur_id','fourniture_id', 'commandes_fichier_id'];

  public function fourniture(){
   return $this->BelongsTo(Fourniture::class);
  }
  public function fournisseur()
  {
    return $this->BelongsTo(Fournisseur::class);
  }
  // public function fichierCommande()
//   {
//    /*  return $this->BelongsTo(Commandes_fichier::class, 'commandes_fichier_id ','id'); */
//  return  Commandes_fichier::where('id',$this->commandes_fichier_id)->get();
//   }
}
