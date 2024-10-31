<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;

class Vale extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function getCreatedAtAttribute($value){
        $date = Carbon::parse($value);
        $date = $date->format('d-m-Y h:i:s');
        return $date;
    }

    protected $fillable = [
        'updated_at', 'created_at', 'idSucursalSalida', 'idSucursalEntrada', 'monto', 'id', 'aceptado', 'observaciones', 'idCalendario', 'idIngreso', 'idEgreso', 'eliminado', 'activo', 'idUsuarioCreo', 'idUsuarioAcepto', 'folio', 'idNivel'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
