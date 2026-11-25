<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class correlativo extends Model
{
    protected $table='correlativos';
    protected $fillable=[
        'correlativo',
        'usuarios_id',
        'activo'
    ];
}
