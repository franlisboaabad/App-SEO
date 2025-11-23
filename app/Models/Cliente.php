<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellidos', 'dni', 'celular', 'email','nombre_tienda', 'direccion','geolocalizacion','latitud','longitud', 'estado'];

    //relaciones

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function ordenes()
    {
        return $this->hasMany(OrdenDeServicio::class);
    }


    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

}
