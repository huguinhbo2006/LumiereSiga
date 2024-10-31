<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Profesore extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'updated_at', 'created_at', 'nombre', 'correo', 'calle', 'numeroInterior', 'numeroExterior', 'ciudad', 'estado', 'codigoPostal', 'colonia', 'telefono', 'celular',
        'nss', 'rfc', 'curp', 'fechaNacimiento', 'fechaAltaIMSS', 'fechaIngreso', 'fechaSalida',
        'actaNacimiento', 'comprobanteDomicilio', 'curpImagen', 'ifef', 'ifet', 'rfcImagen',
        'carta1', 'carta2', 'nssImagen', 'comprobanteEstudios', 'activo', 'eliminado', 'idSucursal',
        'idPuesto', 'idDepartamento', 'idLetra', 'estadoCivil', 'cuentaBancaria', 'idEstado', 'idMunicipio'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
