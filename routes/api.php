<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\FournitureController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatégorieController;
use App\Http\Controllers\ServiceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//routes Auth
Route::post('/login', [authController::class, 'authenticate']);
Route::post('/checkrole', [authController::class, 'checkUtilisateurRole']);

//routes Auth
Route::post('/login', [authController::class, 'authenticate']);
Route::post('/checkrole', [authController::class, 'checkUtilisateurRole']);

Route::group(['middleware' => ['jwt.verify:admin']], function () {
    //routes utilisateurs
    Route::resource('utilisateur', UtilisateurController::class)->only('store', 'destroy','create');
    //routes fourniture
    Route::resource('fourniture', FournitureController::class)->only('store', 'update','destroy', 'create');
    //routes demandes
    Route::resource('demande', DemandeController::class)->only('destroy', 'store','update', 'create');
    Route::post('/demande/upload', [DemandeController::class, 'upload']);
    Route::delete('/demande/delete/{id}', [DemandeController::class, 'destroy_']);
    //routes fournisseur
    Route::resource('fournisseur', FournisseurController::class)->only('store', 'update','destroy', 'create');
    //routes commande
    Route::resource('commande', CommandeController::class)->only('destroy', 'store','update', 'create');
    Route::post('/commande/upload', [CommandeController::class, 'upload']);
    Route::delete('/commande/delete/{id}', [CommandeController::class, 'destroy_']);
    //statistique
    Route::get('/statistique/fournituretopdemandes', [StatistiqueController::class, 'getFournitureTopDemandes']);
    //routes catégories
    Route::resource('categorie', CatégorieController::class)->only('store', 'update','destroy', 'create');                                                                              
    Route::delete('/categorie/delete/{id}', [CatégorieController::class,'destroy_']);
    //routes services
    Route::resource('service', ServiceController::class)->only('store', 'update', 'destroy', 'create');                                                                              

    
});


Route::group(['middleware' => ['jwt.verify:admin,spectateur']], function () {
    //routes utilisateurs
    Route::resource('utilisateur', UtilisateurController::class)->except([ 'store', 'create', 'edit', 'destroy']);
    //routes fourniture
    Route::resource('fourniture', FournitureController::class)->except(['update', 'store', 'create', 'edit', 'destroy']);
    Route::get('/fourniture/get/catégories', [FournitureController::class, 'getCatégories']);
    //routes demandes
    Route::resource('demande', DemandeController::class)->except(['destroy', 'update', 'store', 'edit']);
    Route::get('/demande/get/fichiers', [DemandeController::class, 'getFichiers']);
    //routes fournisseur
    Route::resource('fournisseur', FournisseurController::class)->except(['update', 'store', 'create', 'edit', 'destroy']);
    //routes commande
    Route::resource('commande', CommandeController::class)->except(['destroy', 'update', 'store', 'edit']);
    Route::get('/commande/get/fichiers', [CommandeController::class, 'getFichiers']);
    //routes statistique
    Route::get('/statistique/stock', [StatistiqueController::class, 'getStock']);
    Route::get('/statistique/chercherStock/{value}', [StatistiqueController::class, 'chercherStock']);
    Route::get('/statistique/chercherStock', [StatistiqueController::class, 'getStock']);
    Route::post('/statistique/getutilisateurfourniture', [StatistiqueController::class, 'getUtilisateurFourniture']);
    Route::post('/statistique/getfournisseurfourniture', [StatistiqueController::class, 'getFournisseurFourniture']);
    //routes catégories
    Route::resource('categorie', CatégorieController::class)->except(['destroy', 'update', 'store', 'edit','create']);
    Route::get('/categorie/search/{value}',[ CatégorieController::class,"show_"]);
    //routes services
    Route::resource('service', ServiceController::class)->except('store', 'update', 'destroy', 'create');                                                                              
    //logout
    Route::post('/logout', [authController::class, 'logout']);
});





// Route::resource('utilisateur', UtilisateurController::class)->except(['update','store','create', 'edit']);
// //routes fourniture
// Route::resource('fourniture', FournitureController::class)->except([
//     'create', 'edit'
// ]);
// Route::get('/fourniture/get/catégories', [FournitureController::class, 'getCatégories']);

// //routes demandes
// Route::resource('demande', DemandeController::class)->except([
//      'edit'
// ]);
// Route::post('/demande/upload', [DemandeController::class, 'upload']);
// Route::get('/demande/get/fichiers', [DemandeController::class, 'getFichiers']);
// //routes fournisseur
// Route::resource('fournisseur', FournisseurController::class)->except([
//     'create', 'edit'
// ]);
// //routes commande
// Route::resource('commande', CommandeController::class)->except([
//      'edit'
// ]);
// Route::post('/commande/upload', [CommandeController::class, 'upload']);
// Route::get('/commande/get/fichiers', [CommandeController::class, 'getFichiers']);
// //routes statistique
// Route::get('/statistique/stock', [StatistiqueController::class, 'getStock']);
// Route::get('/statistique/chercherStock/{value}', [StatistiqueController::class, 'chercherStock']);
// Route::get('/statistique/chercherStock', [StatistiqueController::class, 'getStock']);