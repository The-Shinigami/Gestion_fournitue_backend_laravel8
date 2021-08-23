<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Exception;
use Mockery\Undefined;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $utilisateurs_ = Utilisateur::all();
        $utilisateurs = array();
        foreach($utilisateurs_  as $utilisateur){
         array_push($utilisateurs,(object)array('id' => $utilisateur->id,'nom'=> $utilisateur->nom,'prenom' => $utilisateur->prenom,'service'=> $utilisateur->service->service, 'service_id' => $utilisateur->service->id,
                                                 'num_tel' => $utilisateur->num_tel,'role' =>  $utilisateur->role,'login' => $utilisateur->login,'password'=> $utilisateur->password));
        }
        return response()->json(['utilisateurs' => $utilisateurs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ["services" => Service::all()];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    if ( !isset($request->login) && !isset($request->password)) {
       $login = "";
       $password = "";
    }else{
      $login = $request->login;
      $password = $request->password;
    }
    try {
      $state = Utilisateur::create(
        [
          'nom' => $request->nom,
          'prenom' => $request->prenom,
          'service_id' => $request->service_id,
          'num_tel' => $request->num_tel,
          'login' => $login,
          'password' => $password,
          'role' => $request->role
        ]
      );
      if ($state) {
        return ['state' =>'success', "message" => "ajouter avec succés"];
      } else {
        return ['state' =>'failed', "message" => "il'ya un problem, verifier les données"];
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
      $services = Service::where('service', 'LIKE', '%' . $value . '%')->get();
    $utilisateurs =[];
   if($services->count() ===0)
    {  $utilisateurs =  Utilisateur::where(
        'nom','LIKE','%'.$value.'%'
        )->orWhere(
          function($query) use ($value){
        $query->orWhere( 
          'prenom','LIKE','%'.$value.'%' 
        )->orWhere(
          'num_tel','LIKE','%'.$value.'%'  
        )->orWhere(
          'role','LIKE','%'.$value.'%'  
        )
        ;
          }
          
        )->get();}
        else{
  $utilisateurs =  Utilisateur::where(
        'nom','LIKE','%'.$value.'%'
        )->orWhere(
          function($query) use ($value,$services){
            foreach ($services as $service)
        $query->orWhere( 
          'prenom','LIKE','%'.$value.'%' 
        )->orWhere(
          'service_id',$service->id  
        )->orWhere(
          'num_tel','LIKE','%'.$value.'%'  
        )->orWhere(
          'role','LIKE','%'.$value.'%'  
        )
        ;
          }
          
        )->get();
        }
           return response()->json(["utilisateurs" => $utilisateurs]);
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
    public function update(Request $request,$id)
    {
      if(isset($request->password) && isset($request->login)){
      try {
      
        $state =  Utilisateur::where('id', $id)->update([
          "nom" => $request->nom,
          "prenom" => $request->prenom,
          "service_id" => $request->service_id,
          "num_tel" => $request->num_tel,
          'login' => $request->login,
          'password' => $request->password,
          'role' => $request->role
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
      else{
      try {
        $state =  Utilisateur::where('id', $id)->update([
          "nom" => $request->nom,
          "prenom" => $request->prenom,
          "service_id" => $request->service_id,
          "num_tel" => $request->num_tel,
          'role' => $request->role
        ]);
        if ($state) {
          return ['state' => 'success', "message" => "modifier avec succés"];
        } else {
          return ['state' => 'failed', "message" => "il'ya un problem, verifier les données"];
        }
      } catch (Exception $e) {
        return ['state' => 'failed', "message" => $e->getMessage()];
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
    $demandes = Demande::where("utilisateur_id",$id)->get();
    if($demandes->count() == 0){
      Utilisateur::where("id",$id)->delete();
      return ["state" => "success", "message" => "L' utilisateur est supprimer avec succès"];
    }
    else{
      return ["state"=> "failed" ,"message" => "La Suppression A été empêché, Il y'a plusieur demandes attaché a cette Utilisateur"];
    }
    }
}
