<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demande;
use App\Models\Fourniture;
use App\Models\Utilisateur;
use App\Models\Demandes_fichier;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DemandeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        

        $demandes_ = Demande::all();
        $demandes = array();

        foreach ($demandes_  as $demande) {
            //pas de fichier
            if ($demande->demandes_fichier_id !== null) {
                $nom_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->id;
            } else {
                $nom_fichier = "";
                $id_fichier = "";
            }
            //pas de date 
            if ($demande->date !== null) {
                $date = $demande->date;
            } else {
                $date = "";
            }
            //pas de référence
            if ($demande->référence!== null) {
                $référence= $demande->référence;
            } else {
                $référence= "";
            }
          

            array_push($demandes, (object)array(
                'id' => $demande->id, 'date' => $date, 'référence' => $référence, 'quantité' => $demande->quantité, 'quantité_demandé' => $demande->quantité_demandé,
                'utilisateur' => $demande->utilisateur->nom." ". $demande->utilisateur->prenom, 'fourniture' =>  $demande->fourniture->article, 'utilisateur_id' => $demande->utilisateur_id, 'fourniture_id' =>  $demande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'demandes_fichier_id' => $id_fichier
            ));
        }
        return json_encode(['demandes' => $demandes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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


        return [
            'fournitures' => $fournitures,
            'utilisateurs' => Utilisateur::all()
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
            $state = Demande::create([
                'référence' => $request->référence,
                'date' => $request->date,
                'quantité' => $request->quantité,
                'quantité_demandé' => $request->quantité_demandé,
                'utilisateur_id' => $request->utilisateur_id,
                'fourniture_id' => $request->fourniture_id,
                'demandes_fichier_id' => $request->demandes_fichier_id
            ]);
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
        $utilisateurs = Utilisateur::where(
            DB::raw('CONCAT_WS(" ", nom,prenom)'),
            'LIKE',
            '%' . $value . '%'
        )->get();
        $fournitures = Fourniture::where(
            'article',
            'LIKE',
            '%' . $value . '%'
        )->get();
        $demandes_1 = [];
        $demandes_2 = [];
        if ($fournitures->count() != 0 || $utilisateurs->count() != 0) {
            
            if ($utilisateurs->count() != 0 && $fournitures->count() == 0) {
                $demandes_1 = Demande::where(
                    'référence',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                    function ($query) use ($utilisateurs,$value) {
                        foreach ($utilisateurs as $utilisateur)
                            $query->orwhere('utilisateur_id', '=', $utilisateur->id)
                                ->orWhere('date', 'LIKE', '%' . $value . '%')
                                ->orWhere('quantité', 'LIKE', '%' . $value . '%')
                                ->orWhere('quantité_demandé', 'LIKE', '%' . $value . '%');
                    }
                )->get();
                
            
            }
            if ($fournitures->count() != 0 && $utilisateurs->count() == 0) {
          
                $demandes_2 = Demande::where(
                    'référence',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                    function ($query) use ($fournitures,$value) {
                        foreach ($fournitures as $fourniture)
                            $query->orwhere('fourniture_id', '=', $fourniture->id)
                            ->orWhere('date', 'LIKE','%' . $value . '%')
                            ->orWhere( 'quantité','LIKE','%' . $value . '%')
                            ->orWhere('quantité_demandé','LIKE','%' . $value . '%' );
                        })->get();
             

            }
            if ($fournitures->count() != 0 && $utilisateurs->count() != 0) {
                $demandes_1 = Demande::where(
                    'référence',
                    'LIKE',
                    '%' . $value . '%'
                )->orWhere(
                    function ($query) use ($fournitures,$utilisateurs,$value) {
                        foreach ($fournitures as $fourniture)
                            $query->orwhere('fourniture_id', '=', $fourniture->id)
                            ->orWhere('date', 'LIKE','%' . $value . '%')
                            ->orWhere( 'quantité','LIKE','%' . $value . '%')
                            ->orWhere('quantité_demandé','LIKE','%' . $value . '%' )
                            ->orWhere(
                                function ($query) use ($utilisateurs) {
                                foreach ($utilisateurs as $utilisateur)
                                $query->orwhere('utilisateur_id', '=', $utilisateur->id);
                            }
                        );
                        })->get();
                
         
            }
        } else if($fournitures->count() == 0 && $utilisateurs->count() == 0){
            $demandes_1 =Demande::where(
                'référence',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                function ($query) use ($value) {
                   
                        $query->orWhere('date', 'LIKE', '%' . $value . '%')
                            ->orWhere('quantité', 'LIKE', '%' . $value . '%')
                            ->orWhere('quantité_demandé', 'LIKE', '%' . $value . '%');
                }
            )->get();
            
        }
     



        $demandes = array();
       
        foreach ($demandes_1 as $demande) {
            //pas de fichier
            if ($demande->demandes_fichier_id !== null) {
                $nom_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->id;
            } else {
                $nom_fichier = "";
                $id_fichier = "";
            }
            //pas de date 
            if ($demande->date !== null) {
                $date = $demande->date;
            } else {
                $date = "";
            }
            //pas de référence
            if ($demande->référence !== null) {
                $référence = $demande->référence;
            } else {
                $référence = "";
            }


            array_push($demandes, (object)array(
                'id' => $demande->id, 'date' => $date, 'référence' => $référence, 'quantité' => $demande->quantité, 'quantité_demandé' => $demande->quantité_demandé,
                'utilisateur' => $demande->utilisateur->nom . " " . $demande->utilisateur->prenom, 'fourniture' =>  $demande->fourniture->article, 'utilisateur_id' => $demande->utilisateur_id, 'fourniture_id' =>  $demande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'demandes_fichier_id' => $id_fichier
            ));
        }
        foreach ($demandes_2  as $demande) {
            //pas de fichier
            if ($demande->demandes_fichier_id !== null) {
                $nom_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->nom_fichier;
                $id_fichier = Demandes_fichier::where('id', $demande->demandes_fichier_id)->first()->id;
            } else {
                $nom_fichier = "";
                $id_fichier = "";
            }
            //pas de date 
            if ($demande->date !== null) {
                $date = $demande->date;
            } else {
                $date = "";
            }
            //pas de référence
            if ($demande->référence !== null) {
                $référence = $demande->référence;
            } else {
                $référence = "";
            }


            array_push($demandes, (object)array(
                'id' => $demande->id, 'date' => $date, 'référence' => $référence, 'quantité' => $demande->quantité, 'quantité_demandé' => $demande->quantité_demandé,
                'utilisateur' => $demande->utilisateur->nom . " " . $demande->utilisateur->prenom, 'fourniture' =>  $demande->fourniture->article, 'utilisateur_id' => $demande->utilisateur_id, 'fourniture_id' =>  $demande->fourniture->id, 'nom_fichier' =>  $nom_fichier, 'demandes_fichier_id' => $id_fichier
            ));
        }
        return ["demandes" => array_unique($demandes, SORT_REGULAR)];
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

            $state =  Demande::where('id', $id)->update([
                'référence' => $request->référence,
                'date' => $request->date,
                'quantité' => $request->quantité,
                'quantité_demandé' => $request->quantité_demandé,
                'utilisateur_id' => $request->utilisateur_id,
                'fourniture_id' => $request->fourniture_id,
                'demandes_fichier_id' => $request->demandes_fichier_id
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
        $fichier = Demandes_fichier::where('id', $id)->get();
        $file_path = 'C:\xampp\htdocs\Gestion_Fourniture_backend\public\uploads\demandes\\' . $fichier[0]->nom_fichier;
        File::delete($file_path);
        Demande::where('demandes_fichier_id', $id)->delete();
        Demandes_fichier::where('id', $id)->delete();
        return [
            "status" => "success",
            "message" => "Le Fichier Est Supprimer Avec succès"
        ];
    }
    public function destroy_($id)
    {
        
        Demande::where('id', $id)->delete();
        
        return [
            "status" => "success",
            "message" => "La demande est supprimer avec succès"
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

        if($validator->fails()) {
            return response()->json(["status" => "failed", "message" => "Vérifier Votre Fichier(pdf,jpeg,png,jpg,gif,svg)", "errors" => $validator->errors()]);
        }

        if ($request->has('fichier')) {
            $fichier = $request->file('fichier');
            $nom_fichier = $fichier->getClientOriginalName();
            $fichier->move('uploads/demandes', $nom_fichier);
            $exist = Demandes_fichier::where('nom_fichier', $nom_fichier)->get();
            if ($exist->count() == 0) {
                $demandes_fichier = Demandes_fichier::create([
                    'nom_fichier' => $nom_fichier
                ]);
                $response["demandes_fichier_id"] = $demandes_fichier->id;
                $response["nom_fichier"] = $nom_fichier;
                $response["status"] = "success";
                $response["message"] = "Success! fichier(s) uploaded";
            } else {
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
        $fichiers = Demandes_fichier::all();
        return response()->json([
            "status" => "success",
            "count" => count($fichiers),
            "data" => $fichiers
        ]);
    }

}
