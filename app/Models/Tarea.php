<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'actividad_id',
        'nombre_tarea',
        'fecha_inicio',
        'fecha_presentacion',
        'responsable',
        'sustento_de_trabajo',
        'estado_de_tarea',
        'comentario',
        'estado'
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }


}
