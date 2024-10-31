<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Nomina extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'updated_at', 'created_at', 'activo', 'eliminado', 'id', 'folio', 'idEmpleado', 'idCalendario',  'idNivel', 'idSucursal', 'idDepartamento', 'idPuesto', 'estatus', 'quincena', 'fechaInicio', 'fechaFin', 'fechaExpedicion', 'total', 'idBanco', 'observaciones'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
