<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

use App\Models\Demande;
use App\Models\Commande;

use App\Models\Utilisateur;
use App\Models\Fournisseur;
use App\Models\Fourniture;

use App\Models\Commandes_fichier;
use App\Models\Demandes_fichier;

use Illuminate\Database\Eloquent\Casts\ArrayObject ;
class StatistiqueController extends Controller
{
   public function getStock() {
        $fournitures_ = Fourniture::select("article","id")
            ->with(['commandes' => function ($query) {
                $query->select('*');
            }, 'demandes' => function ($query) {
                $query->select('*');
            }])->get();
        $fournitures = Array(); 

foreach($fournitures_ as $fourniture){
  array_push($fournitures,(Object)Array("id"=>$fourniture->id ,"article"=> $fourniture->article,"quantité"=> $fourniture->commandes->sum('quantité') - $fourniture->demandes->sum('quantité') )) ;
}

            return ["fournitures" => $fournitures];
   }
    public function chercherStock($value)
    {
        $fournitures_ = Fourniture::select("article", "id")
        ->with(['commandes' => function ($query) {
            $query->select('*');
        }, 'demandes' => function ($query) {
            $query->select('*');
        }])->get();
        $fournitures = array();
        foreach ($fournitures_ as $fourniture) {
            array_push($fournitures, (object)array("id" => $fourniture->id, "article" => $fourniture->article, "quantité" => $fourniture->commandes->sum('quantité') - $fourniture->demandes->sum('quantité')));
        }
      if(strcmp($value,"") != 0){  $filteredArray = Arr::where($fournitures, function ($val, $key) use ($value) {
            return stripos($val->quantité , $value) !== FALSE || stripos($val->article, $value) !== FALSE;
        });
        $fournitures = array();
        foreach ($filteredArray as $fourniture) {
            array_push($fournitures, (object)array("id" => $fourniture->id, "article" => $fourniture->article, "quantité" => $fourniture->quantité));
        }}

        return ["fournitures" => $fournitures];
    }

    public function getFournitureTopDemandes(){

       $demandes_ = Demande::all()->groupBy('fourniture_id');
        $demandes_label = Array();
        $demandes_value = Array();
        $demandes = Array();
        foreach($demandes_  as $demande){
            array_push($demandes,(object)Array("count" => $demande->count() ,'article' => $demande[0]->fourniture->article));
        }
        $demandes_ = new ArrayObject($demandes);
        $demandes_->uasort(
            function($a,$b){
                if($a->count == $b->count)
               { return 0;}
         return $a->count > $b->count ? -1:1;
            }
        );
        $demandes_ =  array_slice((Array)$demandes_, 0, 10);
        foreach ($demandes_ as $demande){
            $array_label = array();
            if(strlen($demande->article)>20)
         {   for ( $i = 0; $i<strlen($demande->article) ;$i=$i+20){
              array_push($array_label,substr($demande->article, $i,20));
            }
        }else {
                $array_label =  $demande->article;
        }
        
           
            array_push($demandes_label, $array_label);
            array_push($demandes_value,  $demande->count);

        }

       return ["demandes" => $demandes,"labels"=> $demandes_label,"values"=> $demandes_value];
    }
 public function getUtilisateurFourniture(Request $request){
     $from=date($request->date_debut);
     $to=date($request->date_fin);
     $demandes_ = Demande::where('fourniture_id',$request->fourniture_id)->whereBetween('date', [$from, $to])->get();
     $demandes = Array();
      foreach($demandes_ as $demande){
          array_push($demandes,(Object)Array("id" => $demande->id,"date" => $demande->date,"utilisateur" => $demande->utilisateur->nom . " " . $demande->utilisateur->prenom,"quantité" => $demande->quantité));
      }
      
      return ["demandes" => $demandes];
 
    }
    public function getFournisseurFourniture(Request $request)
    {
        $from = date($request->date_debut);
        $to = date($request->date_fin);
        $commandes_ = commande::where('fourniture_id', $request->fourniture_id)->whereBetween('date_livraison', [$from, $to])->get();
        $commandes = array();
        foreach ($commandes_ as $commande) {
            array_push($commandes, (object)array("id"=>$commande->id,"date_livraison" => $commande->date_livraison, "fournisseur" => $commande->fournisseur->nom_société, "quantité" => $commande->quantité));
        }

        return ["commandes" => $commandes];
    }

}
