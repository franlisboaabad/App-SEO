<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenEquipo extends Model
{
    use HasFactory;

    protected $fillable = ['equipo_id', 'file'];


    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
