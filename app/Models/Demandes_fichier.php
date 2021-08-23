<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demandes_fichier extends Model
{
    use HasFactory;
    protected $fillable=['nom_fichier'];
}
