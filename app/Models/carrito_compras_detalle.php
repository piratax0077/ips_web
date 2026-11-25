<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carrito_compras_detalle extends Model
{
    protected $table='carrito_compras_detalle';
    protected $fillable=[
        'repuesto_id',
        'cantidad',
        'carrito_numero'
    ];
}
