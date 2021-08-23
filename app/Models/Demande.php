<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;
    protected $fillable = ['référence','date','quantité','quantité_demandé','utilisateur_id','fourniture_id', 'demandes_fichier_id'];

    public function fourniture()
    {
        return $this->BelongsTo(Fourniture::class);
    }
    public function utilisateur()
    {
        return $this->BelongsTo(Utilisateur::class);
    }
}
