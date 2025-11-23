<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'tipo_id', 'marca', 'modelo', 'numero_de_serie', 'caracteristicas','accesorios_adicionales', 'estado'];


    //relaciones

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoEquipo::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenEquipo::class);
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenDeServicio::class);
    }

}
