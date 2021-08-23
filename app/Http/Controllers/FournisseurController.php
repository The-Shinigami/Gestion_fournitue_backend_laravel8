<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Models\Fournisseur;
use Exception;

class FournisseurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ['fournisseurs' => Fournisseur::all()];
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
            if(isset($request->num_tel) && isset($request->representant)){
                $state = Fournisseur::create([
                    'nom_société' => $request->nom_société,
                    'num_tel' => $request->num_tel,
                    "representant" => $request->representant
                ]);
            } else if (isset($request->num_tel)) {
                $representant="";
                $state = Fournisseur::create([
                    'nom_société' => $request->nom_société,
                    'num_tel' => $request->num_tel,
                    "representant" => $representant
                ]);
            }
            else{
                $representant = "";
                $num_tel = "";
                $state = Fournisseur::create([
                    'nom_société' => $request->nom_société,
                    'num_tel' => $num_tel,
                    "representant" => $representant
                ]);
            }
            

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
         $fournisseurs=  Fournisseur::where(
         'nom_société','LIKE','%'.$value.'%'
        )->orWhere(
         'num_tel','LIKE','%'.$value.'%'
        )->orWhere(
            "representant" ,'LIKE','%'.$value.'%'
        )->get();
           return["fournisseurs" => $fournisseurs];
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
        $state = ['state' => "failed"];
        if(isset($request->num_tel) && isset($request->representant))
       {

            try {

                $state =  Fournisseur::where('id', $id)->update([
                    "nom_société" => $request->nom_société,
                    "num_tel" => $request->num_tel,
                    "representant" => $request->representant
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
        else if(isset($request->num_tel)){

            try {

                $state =  Fournisseur::where('id', $id)->update([
                    "nom_société" => $request->nom_société,
                    "num_tel" => $request->num_tel,
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
        else {

            try {

                $state =  Fournisseur::where('id', $id)->update([
                    "nom_société" => $request->nom_société
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
        
        $commandes = Commande::where("fournisseur_id", $id)->get();
        if ($commandes->count() == 0 ) {
            Fournisseur::where("id", $id)->delete();
            return ["state" => "success", "message" => "Le fournisseur est supprimer avec succès"];
        } else {
            return ["state" => "failed", "message" => "La Suppression a été empêché, Il y'a plusieur commandes attaché a ce fournisseur"];
        }
    }
}
