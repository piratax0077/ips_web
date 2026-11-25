<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class compra_getnet extends Model
{
    protected $table='compra_getnet';
    protected $fillable=[
        'session_id',
        'total',
        'status',
        'usuario_id',
        'numero_carrito',
        'token_ws'
    ];
}
