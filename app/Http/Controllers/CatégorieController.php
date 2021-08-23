<?php

namespace App\Http\Controllers;

use App\Models\Catégorie;
use App\Models\Fourniture;
use App\Models\Sous_catégorie;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;

class CatégorieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sousCatégories_ = Sous_catégorie::all();
        $sousCatégories = Array();
        foreach($sousCatégories_ as $sous_catégorie){
          array_push($sousCatégories,(Object)Array("id" => $sous_catégorie->id ,"catégorie" => $sous_catégorie->catégorie->catégorie, "catégorie_id" => $sous_catégorie->catégorie->id, "sous_catégorie" => $sous_catégorie->sous_catégorie ,"signification" => $sous_catégorie->signification));
        }
      return ["catégories"=> Catégorie::all(),"sousCatégories" =>  $sousCatégories];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ["catégories" => Catégorie::all()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         if ( isset($request->catégorie) && isset($request->signification)){

           
            try {
                $state = Catégorie::create([
                    "catégorie" => $request->catégorie,
                    "signification" => $request->signification
                ]);
                if ($state) {
                    return ['state' =>'success', "message" => "ajouter avec succés"];
                } else {
                    return ['state' =>'failed', "message" => "il'ya un problem, verifier les données"];
                } 
            } catch (Exception $e) {
                return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
            } 
           
         }
        if (isset($request->sous_catégorie) && isset($request->signification) && isset($request->catégorie_id)) {

            try {
                $state = Sous_catégorie::create([
                    "sous_catégorie" => $request->sous_catégorie,
                    "signification" => $request->signification,
                    "catégorie_id" => $request->catégorie_id,
                ]);
                if ($state) {
                    return ['state' => 'success',"message" => "ajouter avec succés"];
                } else {
                    return ['state' =>'failed', "message" => "il' ya un problem, verifier les données"];
                } 
            } catch (Exception $e) {
                return ['state' =>'failed', "message" => "il' ya un problem, verifier les données"];
            }   
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
        $catégories = Catégorie::where("catégorie","like","%".$value."%")->get();
        $sousCatégories_ = "" ;
        if($catégories->count() === 0)
       { $sousCatégories_ = Sous_catégorie::where(
            "sous_catégorie" ,"like","%".$value."%"
        )->orWhere("signification", "like", "%" . $value . "%")->get();}
        else {
            $sousCatégories_ = Sous_catégorie::where(
            "sous_catégorie" ,"like","%".$value."%"
        )->orWhere(
            function ($quary) use ($value, $catégories)
           {
               foreach($catégories as $catégorie)
              {  
                  $quary->orWhere("catégorie_id", $catégorie->id)
                  ->orWhere("signification", "like", "%" . $value . "%");
                }
            })->get();
        }
        $sousCatégories = array();
        foreach ($sousCatégories_ as $sous_catégorie) {
            array_push($sousCatégories, (object)array("id" => $sous_catégorie->id, "catégorie" => $sous_catégorie->catégorie->catégorie, "catégorie_id" => $sous_catégorie->catégorie->id, "sous_catégorie" => $sous_catégorie->sous_catégorie, "signification" => $sous_catégorie->signification));
        }

        return ["sousCatégories" => $sousCatégories];
    }
    public function show_($value){
        $catégories = Catégorie::where(
            "catégorie",
            "like",
            "%" . $value . "%"
        )->orWhere("signification", "like", "%" . $value . "%")->get();

        return ["catégories" => $catégories];
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
        if (isset($request->catégorie) && isset($request->signification)) {
            try {

                $state = Catégorie::where("id", $id)->update([
                    "catégorie" => $request->catégorie,
                    "signification" => $request->signification
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
        if (isset($request->sous_catégorie) && isset($request->signification) && isset($request->catégorie_id)) {
            try {

                $state = Sous_catégorie::where("id", $id)->update([
                    "sous_catégorie" => $request->sous_catégorie,
                    "signification" => $request->signification,
                    "catégorie_id" => $request->catégorie_id,
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fournitures= Fourniture::where("sous_catégorie_id", $id)->get();
        if($fournitures->count() === 0){
            $state =   sous_Catégorie::where("id", $id)->delete();
            if ($state) {
                return ["message" => "la sous catégorie est supprimer avec succés"];
            } else {
                return ["message" => "Il y à un problem"];
            }
        } else {
            return ["message" => "Vous ne pouvez pas supprimer cette sous catégorie, il est liée a plusieur fournitures"];
        } 
    
    }
    public function destroy_($id)
    {
            $sousCatégories = Sous_catégorie::where("catégorie_id",$id)->get();
    if($sousCatégories->count() === 0){
            $state = Catégorie::where("id", $id)->delete();
            if ($state) {
                return ["message" => "la sous catégorie est supprimer avec succés"];
            } else {
                return ["message" => "Il y à un problem"];
            }
    } else{
            return ["message" => "Vous ne pouvez pas supprimer cette catégorie, il est liée a plusieur sous catégorie"];
    } 
    }
}
