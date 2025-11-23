<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;


    protected $fillable = ['proyecto_id','actividad','fecha_facturacion','estado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

}
