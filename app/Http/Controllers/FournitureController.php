<?php

namespace App\Http\Controllers;

use App\Models\Catégorie;
use App\Models\Commande;
use App\Models\Demande;
use Illuminate\Http\Request;
use App\Models\Fourniture;
use App\Models\Sous_catégorie;
use App\Models\Utilisateur;
use Exception;

class FournitureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $fournitures_ = Fourniture::all();
        $fournitures = Array();
        foreach($fournitures_ as $fourniture){
            array_push($fournitures,(Object)Array("id"=> $fourniture->id ,"code"=>$fourniture->code, "catégorie_id" => $fourniture->catégorie->id,"catégorie" =>$fourniture->catégorie->catégorie,"sous_catégorie"=>$fourniture->sous_catégorie->sous_catégorie, "sous_catégorie_id" => $fourniture->sous_catégorie->id,"article" => $fourniture->article));
        }
    


        return [ 'fournitures' => $fournitures];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

            $state = Fourniture::create([
                'code' => $request->code,
                'catégorie_id' => $request->catégorie_id,
                'sous_catégorie_id' => $request->sous_catégorie_id,
                'article' => $request->article
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
    $catégories_= Catégorie::where('catégorie','LIKE','%'.$value.'%')->get();
    $sousCatégories_ = Sous_catégorie::where('sous_catégorie', 'LIKE', '%' . $value . '%')->get();
    $fournitures_ = [];
    $fournitures_2 = [];
    if($catégories_->count() == 0 && $sousCatégories_->count() != 0){
            $fournitures_ =  Fourniture::where(
                'code',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                function ($query) use ($sousCatégories_, $value) {
                    foreach
                    ($sousCatégories_ as $sous_catégorie)
                        $query->where('sous_catégorie_id', '=', $sous_catégorie->id)
                            ->orWhere(
                                'article',
                                'LIKE',
                                '%' . $value . '%'
                            );
                })->get();
    } else if ($catégories_->count() != 0 && $sousCatégories_->count() == 0) {
            $fournitures_ =  Fourniture::where(
                'code',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                function ($query) use ($catégories_,$value) {
                    foreach ($catégories_ as $catégorie)
                        $query->where('catégorie_id', '=', $catégorie->id)
                    ->orWhere(
                        'article',
                        'LIKE',
                        '%' . $value . '%'
                    );
                }
            )->get();
    }
        if ($catégories_->count() != 0 && $sousCatégories_->count() != 0) {
            $fournitures_2 =  Fourniture::where(
                'code',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                function ($query) use ($catégories_, $sousCatégories_, $value) {
                    foreach ($catégories_ as $catégorie)
                   {     foreach ($sousCatégories_ as $sous_catégorie)
                     {   $query->where('catégorie_id', '=', $catégorie->id)
                            ->orWhere('article','LIKE','%' . $value . '%')
                            ->orWhere('sous_catégorie_id', '=', $sous_catégorie->id);
                }};
                }
            )->get();
        }
        else if ($catégories_->count() == 0 && $sousCatégories_->count() == 0){
            $fournitures_2 =  Fourniture::where(
                'code',
                'LIKE',
                '%' . $value . '%'
            )->orWhere(
                function ($query) use ( $value) {
 $query->orWhere('article', 'LIKE', '%' . $value . '%');
              
                }
            )->get();
        }

        $fournitures = array();
        foreach ($fournitures_ as $fourniture) {
            array_push($fournitures, (object)array("id" => $fourniture->id, "code" => $fourniture->code, "catégorie_id" => $fourniture->catégorie->id, "catégorie" => $fourniture->catégorie->catégorie, "sous_catégorie" => $fourniture->sous_catégorie->sous_catégorie, "sous_catégorie_id" => $fourniture->sous_catégorie->id, "article" => $fourniture->article));
        }
        foreach ($fournitures_2 as $fourniture) {
            array_push($fournitures, (object)array("id" => $fourniture->id, "code" => $fourniture->code, "catégorie_id" => $fourniture->catégorie->id, "catégorie" => $fourniture->catégorie->catégorie, "sous_catégorie" => $fourniture->sous_catégorie->sous_catégorie, "sous_catégorie_id" => $fourniture->sous_catégorie->id, "article" => $fourniture->article));
        }
    

           return["fournitures" => array_unique($fournitures, SORT_REGULAR)];
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

            $state =  Fourniture::where('id', $id)->update([
                "code" => $request->code,
                "catégorie_id" => $request->catégorie_id,
                "sous_catégorie_id" => $request->sous_catégorie_id,
                "article" => $request->article
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
        $demandes = Demande::where("fourniture_id", $id)->get();
        $commandes = Commande::where("fourniture_id", $id)->get();
        if ($demandes->count() == 0 && $commandes->count() == 0) {
            Fourniture::where("id", $id)->delete();
            return ["state" => "success", "message" => "La fourniture est supprimer avec succès"];
        } else {
            return ["state" => "failed", "message" => "La Suppression A été empêché, Il y'a plusieur demandes ou bien commandes attaché a cette fourniture"];
        }
    }
    public function getCatégories()
    {
        
        return ["catégories" => Catégorie::all()];
    }
}
