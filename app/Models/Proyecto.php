<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id','nombre_proyecto','estado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'proyecto_usuario');
    }

}
