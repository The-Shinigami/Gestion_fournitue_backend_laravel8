<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commandes_fichier extends Model
{
    use HasFactory;

    protected  $table = "commandes_fichiers";
    protected $fillable = ['nom_fichier'];

    
}
