<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Utilisateur;
use Exception;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ["services" => Service::all()];
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

           
            $state = Service::create( [
               "service" => $request->service 
            ] );

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
        $services = Service::where("service","like","%".$value."%")->get();
           
        return ["services" => $services]; 
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

            $state = Service::where("id", $id)->update([
                "service" => $request->service,

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
        $utilisateurs = Utilisateur::where("service_id", $id)->get();
        if ($utilisateurs->count() === 0) {
            $state =   Service::where("id", $id)->delete();
            if ($state) {
                return ["message" => "le service est supprimer avec succés"];
            } else {
                return ["message" => "Il y à un problem"];
            }
        } else {
            return ["message" => "Vous ne pouvez pas supprimer cette service, il est liée a plusieur utilisateurs"];
        } 
    }
}
