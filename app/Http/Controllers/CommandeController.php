<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Fourniture;
use Illuminate\Http\Request;
use App\Models\Commandes_fichier;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $commandes_ = Commande::all();
        $commandes = array();
       
        foreach ($commandes_  as $commande) {
            //pas de fichier
            if ($commande->commandes_fichier_id !== null) {
                $nom_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->id;
            }else{
                $nom_fichier ="";
                $id_fichier = "";
            }
            //pas de date de livraison
            if ($commande->date_livraison !== null) {
                $date_livraison = $commande->date_livraison;
            } else {
                $date_livraison = "";
            }
            //pas de numero de commande
            if ($commande->numero_bon_commande !== null) {
                $numero_bon_commande = $commande->numero_bon_commande;
            } else {
                $numero_bon_commande = "";
            }
            //pas de fournisseur
            if ($commande->fournisseur_id!== null) {
                $fournisseur = $commande->fournisseur->nom_société;
                $fournisseur_id =$commande->fournisseur->id;
            } else {
                $fournisseur = "";
                 $fournisseur_id ="";
            }

                array_push($commandes, (object)array(
                    'id' => $commande->id, 'date_livraison' => $date_livraison, 'numero_bon_commande' => $numero_bon_commande, 'quantité' => $commande->quantité,
                    'fournisseur' => $fournisseur, 'fourniture' =>  $commande->fourniture->article, 'fournisseur_id' => $fournisseur_id, 'fourniture_id' =>  $commande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'commandes_fichier_id' => $id_fichier
                ));
              
           

           
        }
        return json_encode(['commandes' => $commandes]);
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return [
'fournitures' => Fourniture::all(),
'fournisseurs' => Fournisseur::all()
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        try {
            $state = Commande::create(
                [
                    'date_livraison' => $request->date_livraison,
                    'numero_bon_commande' => $request->numero_bon_commande,
                    'quantité' => $request->quantité,
                    'fournisseur_id' => $request->fournisseur_id,
                    'fourniture_id' => $request->fourniture_id,
                    'commandes_fichier_id' => $request->commandes_fichier_id
                ]
            );
            if ($state) {
                return ['state' => 'success', "message" => "ajouter avec succés"];
            } else {
                return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
            }
        } catch (Exception $e) {
            return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
        }   
     
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($value)
    {
       $fournisseurs = Fournisseur::where(
           'nom_société','LIKE','%'.$value. '%'
       )->get();
        $fournitures = Fourniture::where(
            'article','LIKE','%' . $value . '%'
        )->get();
        $commandes_1 =[];
        $commandes_2 =[];
        $a=["b"];
        if($fournisseurs->count() != 0 || $fournitures->count() != 0){
     if($fournisseurs->count() != 0 && $fournitures->count() == 0) {
              
                $commandes_1 = Commande::where(
                    'date_livraison',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                     function ($query) use ($fournisseurs,$value) {
                        foreach ($fournisseurs as $fournisseur)
                            $query->orwhere('fournisseur_id', '=', $fournisseur->id)
                                  ->orWhere('numero_bon_commande', 'LIKE','%' . $value . '%')
                                  ->orWhere( 'quantité',$value);
                    }
                )->get(); 
                   
               
            }
            if($fournitures->count() != 0 && $fournisseurs->count() == 0) {
                $a = ["a"];
                $commandes_2 = Commande::where(
                    'date_livraison',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                    function ($query) use ($fournitures, $value) {
                        foreach ($fournitures as $fourniture)
                            $query->orwhere('fourniture_id', $fourniture->id)
                                ->orWhere('numero_bon_commande', 'LIKE', '%' . $value . '%')
                                ->orWhere('quantité', $value);
                    }
                )->get(); 
               
            
            }
            if($fournitures->count() != 0 && $fournisseurs->count() != 0){
                $commandes_1 = Commande::where(
                    'date_livraison',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                    function ($query) use ($fournisseurs, $fournitures,$value) {
                        foreach ($fournisseurs as $fournisseur)
                            $query->orwhere('fournisseur_id', '=', $fournisseur->id)
                            ->orwhere('numero_bon_commande','LIKE','%' . $value . '%')
                        ->orWhere('quantité',$value)
                        ->orWhere( function ($query) use ($fournitures) {
                                foreach ($fournitures as $fourniture)
                                $query->orwhere('fourniture_id', '=', $fourniture->id);
                            }
                        );
                    }
                )->get();
                   
            }
        }else if($fournitures->count() == 0 && $fournisseurs->count() == 0){
            $commandes_1 = Commande::where(
                'date_livraison',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                'numero_bon_commande',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                'quantité',
                '=',
                (int)$value
            )->get();
        }
  
    
       
      

        $commandes = array();
        foreach ($commandes_1  as $commande) {
            
            if ($commande->commandes_fichier_id !== null) {
                $nom_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->id;
            } else {
                $nom_fichier = "";
                $id_fichier = "";
            }
            //pas de date de livraison
            if ($commande->date_livraison !== null) {
                $date_livraison = $commande->date_livraison;
            } else {
                $date_livraison = "";
            }
            //pas de numero de commande
            if ($commande->numero_bon_commande !== null) {
                $numero_bon_commande = $commande->numero_bon_commande;
            } else {
                $numero_bon_commande = "";
            }
            //pas de fournisseur
            if ($commande->fournisseur_id !== null) {
                $fournisseur = $commande->fournisseur->nom_société;
                $fournisseur_id = $commande->fournisseur->id;
            } else {
                $fournisseur = "";
                $fournisseur_id = "";
            }

            array_push($commandes, (object)array(
                'id' => $commande->id, 'date_livraison' => $date_livraison, 'numero_bon_commande' => $numero_bon_commande, 'quantité' => $commande->quantité,
                'fournisseur' => $fournisseur, 'fourniture' =>  $commande->fourniture->article, 'fournisseur_id' => $fournisseur_id, 'fourniture_id' =>  $commande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'fichier_id' => $id_fichier
            ));
              
           
        }
        foreach ($commandes_2  as $commande) {
       
            if ($commande->commandes_fichier_id !== null) {
                $nom_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Commandes_fichier::where('id', $commande->commandes_fichier_id)->first()->id;
            } else {
                $nom_fichier = "";
                $id_fichier = "";
            }
            //pas de date de livraison
            if ($commande->date_livraison !== null) {
                $date_livraison = $commande->date_livraison;
            } else {
                $date_livraison = "";
            }
            //pas de numero de commande
            if ($commande->numero_bon_commande !== null) {
                $numero_bon_commande = $commande->numero_bon_commande;
            } else {
                $numero_bon_commande = "";
            }
            //pas de fournisseur
            if ($commande->fournisseur_id !== null) {
                $fournisseur = $commande->fournisseur->nom_société;
                $fournisseur_id = $commande->fournisseur->id;
            } else {
                $fournisseur = "";
                $fournisseur_id = "";
            }

            array_push($commandes, (object)array(
                'id' => $commande->id, 'date_livraison' => $date_livraison, 'numero_bon_commande' => $numero_bon_commande, 'quantité' => $commande->quantité,
                'fournisseur' => $fournisseur, 'fourniture' =>  $commande->fourniture->article, 'fournisseur_id' => $fournisseur_id, 'fourniture_id' =>  $commande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'fichier_id' => $id_fichier
            ));
              
           
        }
           return["commandes" => array_unique($commandes, SORT_REGULAR)];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $state =  Commande::where('id', $id)->update([
                'date_livraison' => $request->date_livraison,
                'numero_bon_commande' => $request->numero_bon_commande,
                'quantité' => $request->quantité,
                'fournisseur_id' => $request->fournisseur_id,
                'fourniture_id' => $request->fourniture_id,
                'commandes_fichier_id' => $request->commandes_fichier_id
            ]);
            if ($state) {
                return ['state' => 'success', "message" => "modifier avec succés"];
            } else {
                return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
            }
        } catch (Exception $e) {
            return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
        } 
          
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) 
    {
        $fichier = Commandes_fichier::where('id', $id)->get();
        $file_path = 'C:\xampp\htdocs\Gestion_Fourniture_backend\public\uploads\\'. $fichier[0]->nom_fichier;
        File::delete($file_path);
        Commande::where('commandes_fichier_id',$id)->delete();
        Commandes_fichier::where('id',$id)->delete();
        return ["status" =>"success",
    "message" => "Le Fichier Est Supprimer Avec succès"];
    }
    public function destroy_($id)
    {

        Commande::where('id', $id)->delete();

        return [
            "status" => "success",
            "message" => "La commmande est supprimer avec succès"
        ];
    }
    public function upload(Request $request)
    {
        $nom_fichier = [];
        $response = [];

        $validator = Validator::make(
            [
                'fichier' =>  $request->fichier
            ],
            [
                'fichier' => 'required|mimes:pdf,jpeg,png,jpg,gif,svg|max:5120'
            ]
        );

        if ($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "Vérifier Votre Fichier(pdf,jpeg,png,jpg,gif,svg)", "errors" => $validator->errors()]);
        }

        if ($request->has('fichier')) {
                $fichier = $request->file('fichier');
                $nom_fichier = $fichier->getClientOriginalName();
                $fichier->move('uploads/commandes/', $nom_fichier);
            $exist = Commandes_fichier::where('nom_fichier', $nom_fichier)->get();
                   if($exist->count() == 0){
                $commandes_fichier = Commandes_fichier::create([
                    'nom_fichier' => $nom_fichier
                ]);
                $response["commandes_fichier_id"] = $commandes_fichier->id;
                $response["nom_fichier"]  = $nom_fichier;
                $response["status"] = "success";
                $response["message"] = "Success! fichier(s) uploaded";
                   }
                   else{
                $response["status"] = "failed";
                $response["message"] = "Le nom de Fichier Exist Deja Renomer Votre Fichier est Resseyez";
                   }
            
            
            
        } else {
            $response["status"] = "failed";
            $response["message"] = "Failed! fichier(s) not uploaded";
        }
        return response()->json($response);
    }
    
    public function getFichiers()
    {
        $fichiers = Commandes_fichier::all();
        return response()->json([
         "status" => "success",
         "count" => count($fichiers),
          "data" => $fichiers
        ]);
    }
}
