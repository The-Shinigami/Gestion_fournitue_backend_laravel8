<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sous_catégorie extends Model
{
    use HasFactory;
    protected $fillable = ['sous_catégorie', 'signification','catégorie_id'];
    public function catégorie()
    {
        return $this->BelongsTo(Catégorie::class);
    }
}
