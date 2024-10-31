<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;

class Ingreso extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }
    
    protected $fillable = [
        'id', 'concepto', 'monto', 'observaciones', 'idRubro', 'idTipo', 'idSucursal', 'idCalendario', 'idFormaPago', 'idMetodoPago', 'idUsuario', 'activo', 'eliminado', 'created_at', 'update_at', 'referencia', 'folio', 'idNivel', 'imagen', 'idBanco', 'nombreCuenta', 'numeroReferencia', 'idCuenta', 'fecha', 'auditado'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
