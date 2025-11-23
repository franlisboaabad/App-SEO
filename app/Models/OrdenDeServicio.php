<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDeServicio extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id','equipo_id', 'fecha_aproximada_entrega', 'descripcion_del_problema','estado_del_servicio','costo_estimado','estado'];


    //relaciones de uno a muchos

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }


}
