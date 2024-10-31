<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;
use App\Sucursale;

class Ficha extends Model implements  AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    public function getFolioAttribute($value){
        $ficha = Ficha::where('folio', '=', $value)->get()[0];
        $sucursalImparticion = Sucursale::find($ficha->idSucursalImparticion);
        return $value.$sucursalImparticion->abreviatura;
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y H:i:s');
    }

    protected $fillable = [
        'id', 'idAlumno', 'semana', 'idGrupo', 'idSucursalImparticion', 'idSucursalInscripcion', 'idUsuario', 'activo', 'eliminado', 'created_at', 'update_at', 'idCalendario', 'folio', 'idNivel', 'intentos', 'observaciones', 'idUsuarioInformacion', 'fecha', 'idTipoPago', 'numeroRegistro'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
