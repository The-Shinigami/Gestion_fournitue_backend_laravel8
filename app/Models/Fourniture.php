<?php

namespace App\Models;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fourniture extends Model
{
    use HasFactory;
    protected $fillable = ['code','catégorie_id','sous_catégorie_id','article'];
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
    public function catégorie()
    {
        return $this->BelongsTo(Catégorie::class);
    }
    public function sous_catégorie()
    {
        return $this->BelongsTo(Sous_catégorie::class);
    }
}
