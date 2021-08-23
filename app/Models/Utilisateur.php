<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Utilisateur extends  Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $fillable =['nom','prenom','service_id','num_tel','role','password','login'];
    protected $table = "utilisateurs";
    public function service()
    {
        return $this->BelongsTo(Service::class);
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
}
