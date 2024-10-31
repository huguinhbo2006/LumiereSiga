<?php

namespace App\Http\Controllers;
use App\Cita;
use App\Alumno;
use App\Alumnodomicilio;
use App\Alumnoabono;
use App\Alumnocargo;
use App\Tutore;
use App\Datosescolare;
use App\Aspiracione;
use App\Ficha;
use App\Ingreso;
use App\Altacurso;
use App\Calendario;
use App\Nivele;
use App\Subnivele;
use App\Categoria;
use App\Modalidade;
use App\Curso;
use App\Turno;
use App\Grupo;
use App\Horario;
use App\Sedesucursale;
use App\Sucursale;
use App\Cursosparidade;
use App\Reservacionesaula;
use App\Aula;
use App\Alumnodescuento;
use App\Alumnofiscale;
use App\Centrosuniversitario;
use App\Sede;
use App\Medioscontacto;
use App\Mediospublicitario;
use App\Viaspublicitaria;
use App\Motivosinscripcione;
use App\Campania;
use App\Motivosbachillerato;
use App\Empresascurso;
use App\Publicitario;
use App\Cupone;
use App\Empleado;
use App\Usuario;
use App\Prospecto;
use App\Seguimiento;
use App\Seguimientodescripcione;
use App\Sexo;
use App\Estado;
use App\Municipio;
use App\Tipoescuela;
use App\Escuela;
use App\Universidade;
use App\Carrera;
use App\Metodospago;
use App\Formaspago;
use App\Banco;
use App\Cuenta;
use App\Conceptosabono;
use App\Conceptoscargo;
use App\Conceptosdescuento;
use App\Tipopago;
USE App\Empresaconvenio;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
include "funciones/FuncionesGenerales.php";

class CRMController extends BaseController
{   
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    function nuevoProspecto(Request $request){
        try {
            $celular = $request['celular'];
            $nombre = $request['nombre'];
            $apellidoPaterno = $request['apellidoPaterno'];
            $apellidoMaterno = $request['apellidoMaterno'];
            $consulta = "SELECT * FROM prospectos where celular = '$celular' OR (nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno')";
            $existe = DB::select($consulta, array());
            if(count($existe) > 0){
                $existe[0]->existo = true;
                return response()->json($existe[0], 200);
            }else{
                $prospecto = Prospecto::create([
                    'nombre' => $request['nombre'],
                    'celular' => $request['celular'],
                    'apellidoPaterno' => $request['apellidoPaterno'],
                    'apellidoMaterno' => $request['apellidoMaterno'],
                    'promedio' => $request['promedio'],
                    'idUsuario' => $request['idUsuario'],
                    'linkFacebook' => $request['linkFacebook'],
                    'linkInstagram' => $request['linkInstagram'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $prospecto->existo = false;
                return response()->json($prospecto, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarProspecto(Request $request){
        try {

            $prospecto = Prospecto::find($request['id']);
            $prospecto->nombre = $request['nombre'];
            $prospecto->celular = $request['celular'];
            $prospecto->apellidoPaterno = $request['apellidoPaterno'];
            $prospecto->apellidoMaterno = $request['apellidoMaterno'];
            $prospecto->promedio = $request['promedio'];
            $prospecto->linkFacebook = $request['linkFacebook'];
            $prospecto->linkInstagram = $request['linkInstagram'];
            $prospecto->save();
            return response()->json($prospecto, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrarProspectos(Request $request){
        try {
            $consulta = Prospecto::join('usuarios', 'prospectos.idUsuario', '=', 'usuarios.id')->
            join('empleados', 'empleados.id', '=', 'usuarios.idEmpleado')->
            select(
                'prospectos.*',
                'empleados.nombre as empleado',
                DB::raw("(SELECT id FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) AS seguimiento"),
                DB::raw("(
                        CASE 
                        WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 0) THEN 'bg-azul'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 1) THEN 'bg-amarillo'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 2) THEN 'bg-verde'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 3) THEN 'bg-rojo'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 4) THEN 'bg-morado'
                       END
                    ) AS bg"),
                DB::raw("(
                        CASE 
                        WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 0) THEN 'PROSPECTO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 1) THEN 'INTERESADO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 2) THEN 'INSCRITO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 3) THEN 'NO INTERESADO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 4) THEN 'PROXIMO CALENDARIO'
                       ELSE 'SIN SEGUIMIENTO'
                       END
                    ) AS estatus"),
                DB::raw("(CASE MONTH(prospectos.created_at) 
                    WHEN 1 THEN 'Enero'
                    WHEN 2 THEN  'Febrero'
                    WHEN 3 THEN 'Marzo' 
                    WHEN 4 THEN 'Abril' 
                    WHEN 5 THEN 'Mayo'
                    WHEN 6 THEN 'Junio'
                    WHEN 7 THEN 'Julio'
                    WHEN 8 THEN 'Agosto'
                    WHEN 9 THEN 'Septiembre'
                    WHEN 10 THEN 'Octubre'
                    WHEN 11 THEN 'Noviembre'
                    WHEN 12 THEN 'Diciembre'
                     END)  as mes")
            )->get();
            return response()->json($consulta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerProspecto(Request $request){
        try {
            $hoy = Carbon::now();
            $prospecto = Prospecto::find($request['id']);
            $seguimientos = Seguimiento::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select(
                'seguimientos.*',
                'calendarios.nombre as calendario',
                DB::raw("(CASE 
                        WHEN(seguimientos.estatus = 0) THEN 'bg-info'
                        WHEN(seguimientos.estatus = 1) THEN 'bg-yellow'
                        WHEN(seguimientos.estatus = 2) THEN 'bg-success'
                        WHEN(seguimientos.estatus = 3) THEN 'bg-danger'
                        WHEN(seguimientos.estatus = 4) THEN 'bg-purple'
                        END) AS bg"),
            )->
            where('idProspecto', '=', $prospecto->id)->get();
            $listas['calendarios'] = Calendario::where('eliminado', '=', 0)->whereRaw('fin > NOW()')->get();
            $listas['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $listas['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $listas['categorias'] = Categoria::where('eliminado', '=', 0)->get();
            $listas['modalidades'] = Modalidade::where('eliminado', '=', 0)->get();
            $listas['cursos'] = Curso::where('eliminado', '=', 0)->get();
            $listas['universidades'] = Universidade::where('eliminado', '=', 0)->get();
            $listas['centros'] = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $listas['carreras'] = Carrera::where('eliminado', '=', 0)->get();
            $listas['medios'] = Medioscontacto::where('eliminado', '=', 0)->get();
            $listas['grupos'] = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
            join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select(
                'grupos.id as id',
                'altacursos.idCalendario',
                'altacursos.idNivel',
                'altacursos.idSubnivel',
                'altacursos.idCategoria',
                'altacursos.idModalidad',
                'altacursos.idSede',
                'altacursos.idCurso',
                'altacursos.inicio',
                'altacursos.fin',
                'altacursos.limitePago',
                'altacursos.precio',
                'cursos.nombre as curso',
                'grupos.idHorario', 
                'grupos.idTurno')->
            where('calendarios.fin', '>', $hoy)->get();

            $respuesta['prospecto'] = $prospecto;
            $respuesta['seguimientos'] = $seguimientos;
            $respuesta['listas'] = $listas;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscarProspecto(Request $request){
        try {
            $busqueda = $request['busqueda'];
            $consulta = Prospecto::join('usuarios', 'prospectos.idUsuario', '=', 'usuarios.id')->
            join('empleados', 'empleados.id', '=', 'usuarios.idEmpleado')->
            select(
                'prospectos.*',
                'empleados.nombre as empleado',
                DB::raw("(SELECT id FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) AS seguimiento"),
                DB::raw("(
                        CASE 
                        WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 0) THEN 'bg-azul'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 1) THEN 'bg-amarillo'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 2) THEN 'bg-verde'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 3) THEN 'bg-rojo'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 4) THEN 'bg-morado'
                       END
                    ) AS bg"),
                DB::raw("(
                        CASE 
                        WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 0) THEN 'PROSPECTO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 1) THEN 'INTERESADO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 2) THEN 'INSCRITO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 3) THEN 'NO INTERESADO'
                       WHEN((SELECT estatus FROM seguimientos WHERE idProspecto = prospectos.id ORDER BY id DESC LIMIT 1) = 4) THEN 'PROXIMO CALENDARIO'
                       ELSE 'SIN SEGUIMIENTO'
                       END
                    ) AS estatus"),
                DB::raw("(CASE MONTH(prospectos.created_at) WHEN 1 THEN 'Enero' WHEN 2 THEN  'Febrero' WHEN 3 THEN 'Marzo' WHEN 4 THEN 'Abril' WHEN 5 THEN 'Mayo' WHEN 6 THEN 'Junio' WHEN 7 THEN 'Julio' WHEN 8 THEN 'Agosto' WHEN 9 THEN 'Septiembre' WHEN 10 THEN 'Octubre' WHEN 11 THEN 'Noviembre' WHEN 12 THEN 'Diciembre'
                     END)  as mes")
            )->whereRaw("CONCAT(prospectos.nombre, ' ', prospectos.apellidoPaterno, ' ', prospectos.apellidoMaterno) LIKE '%$busqueda%' OR prospectos.celular = '%$busqueda%'")->get();
            return response()->json($consulta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarProspecto(Request $request){
        try {
            $prospecto = Prospecto::find($request['id']);
            $prospecto->delete();
            return response()->json($prospecto, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarSeguimiento(Request $request){
        try {
            $prospecto = $request['idProspecto'];
            $consulta = "SELECT * FROM seguimientos WHERE (estatus = 0 OR estatus = 1 OR estatus = 3) AND idProspecto = $prospecto";
            $existe = DB::select ($consulta, array());
            if(count($existe) > 0){
                return response()->json('El prospecto tiene un segumiento abierto', 400);
            }else{
                $seguimiento = Seguimiento::create([
                    'idUsuario' => $request['idUsuario'],
                    'idProspecto' => $request['idProspecto'],
                    'idUniversidad' => (intval($request['idNivel']) === 2) ? 0 : $request['idUniversidad'],
                    'idCentroUniversitario' => (intval($request['idNivel']) === 2) ? 0 : $request['idCentroUniversitario'],
                    'idCarrera' => (intval($request['idNivel']) === 2) ? 0 : $request['idCarrera'],
                    'idNivel' => $request['idNivel'],
                    'idSubnivel' => $request['idSubnivel'],
                    'idCategoria' => $request['idCategoria'],
                    'idModalidad' => $request['idModalidad'],
                    'idCurso' => $request['idCurso'],
                    'idCalendario' => $request['idCalendario'],
                    'idMedioContacto' => $request['idMedioContacto'],
                    'estatus' => 0,
                    'idFicha' => 0,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $seguimiento->existe = false;
                switch($seguimiento->estatus){
                    case 0:
                        $seguimiento->bg = 'bg-info';
                        break;
                    case 1:
                        $seguimiento->bg = 'bg-yellow';
                        break;
                    case 2:
                        $seguimiento->bg = 'bg-success';
                        break;
                    case 3:
                        $seguimiento->bg = 'bg-danger';
                        break;
                    case 4:
                        $seguimiento->bg = 'bg-purple';
                        break;
                }
                $seguimiento->calendario = Calendario::find($seguimiento->idCalendario)->nombre;
                return response()->json($seguimiento, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarSeguimiento(Request $request){
        try {
            $seguimiento = Seguimiento::find($request['id']);
            $seguimiento->idCalendario = $request['idCalendario'];
            $seguimiento->idNivel = $request['idNivel'];
            $seguimiento->idSubnivel = $request['idSubnivel'];
            $seguimiento->idCategoria = $request['idCategoria'];
            $seguimiento->idModalidad = $request['idModalidad'];
            $seguimiento->idCurso = $request['idCurso'];
            $seguimiento->idUniversidad = $request['idUniversidad'];
            $seguimiento->idCentroUniversitario = $request['idCentroUniversitario'];
            $seguimiento->idCarrera = $request['idCarrera'];
            $seguimiento->idMedioContacto = $request['idMedioContacto'];
            if(intval($seguimiento->estatus) !== intval($request['estatus'])){
                $estatusFinal = '';
                if(intval($request['estatus']) === 0)
                    $estatusFinal = 'Prospecto';
                if(intval($request['estatus']) === 1)
                    $estatusFinal = 'Interesado';
                if(intval($request['estatus']) === 2)
                    $estatusFinal = 'Inscrito';
                if(intval($request['estatus']) === 3)
                    $estatusFinal = 'No Interesado';
                if(intval($request['estatus']) === 4)
                    $estatusFinal = 'Proximo Calendario';

                $descripcion = Seguimientodescripcione::create([
                    'idUsuario' => $request['idUsuario'],
                    'idSeguimiento' => $seguimiento->id,
                    'comentario' => 'Se cambio a estatus '.$estatusFinal ,
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 7,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => $seguimiento->estatus,
                    'activo' => 1,
                    'eliminado' => 0
                ]); 
            }
            $seguimiento->estatus = $request['estatus'];
            $seguimiento->save();
            switch($seguimiento->estatus){
                case 0:
                    $seguimiento->bg = 'bg-info';
                    break;
                case 1:
                    $seguimiento->bg = 'bg-yellow';
                    break;
                case 2:
                    $seguimiento->bg = 'bg-success';
                    break;
                case 3:
                    $seguimiento->bg = 'bg-danger';
                    break;
                case 4:
                    $seguimiento->bg = 'bg-purple';
                    break;
            }
            $seguimiento->calendario = Calendario::find($seguimiento->idCalendario)->nombre;
            return response()->json($seguimiento, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor');
        }
    }

    function traerSeguimiento(Request $request){
        try {
            $hoy = Carbon::now();
            $seguimiento = Seguimiento::find($request['id']);
            $prospecto = Prospecto::find($seguimiento->idProspecto);
            $lista = array();
            $descripciones = Seguimientodescripcione::where('idSeguimiento', '=', $request['id'])->get();
            foreach ($descripciones as $descripcion) {
                if(intval($descripcion->medio) === 1)
                    $descripcion->logo = 'fab fa-whatsapp text-success';
                else if(intval($descripcion->medio) === 2)
                    $descripcion->logo = 'fab fa-facebook text-primary';
                else if(intval($descripcion->medio) === 3)
                    $descripcion->logo = 'fab fa-instagram text-pink';
                else if(intval($descripcion->medio) === 4)
                    $descripcion->logo = 'fab fa-telegram text-info';
                else if(intval($descripcion->medio) === 5)
                    $descripcion->logo = 'fas fa-phone text-danger';
                else if(intval($descripcion->medio) === 6)
                    $descripcion->logo = 'fas fa-people-arrows text-purple';
                else if(intval($descripcion->medio) === 7)
                    $descripcion->logo = 'fas fa-edit text-cyan';
                else if(intval($descripcion->medio) === 8)
                    $descripcion->logo = 'fas fa-calendar text-info';
                else if(intval($descripcion->medio) === 9)
                    $descripcion->logo = 'fas fa-calendar-check text-success';
                else if(intval($descripcion->medio) === 10)
                    $descripcion->logo = 'fas fa-calendar-times text-danger';
                else if(intval($descripcion->medio) === 11)
                    $descripcion->logo = 'fas fa-user-times text-danger';
                else if(intval($descripcion->medio) === 12)
                    $descripcion->logo = 'fas fa-check-circle text-success';
                else
                    $descripcion->logo = 'fas fa-percent text-black';


                if(intval($descripcion->estatusSeguimiento) === 0){
                    $descripcion->nombreEstatusSeguimiento = 'Prospecto';
                    $descripcion->colorEstatusSeguimiento  = 'text-info';
                }
                if(intval($descripcion->estatusSeguimiento) === 1){
                    $descripcion->nombreEstatusSeguimiento = 'Interesado';
                    $descripcion->colorEstatusSeguimiento  = 'text-yellow';
                }
                if(intval($descripcion->estatusSeguimiento) === 2){
                    $descripcion->nombreEstatusSeguimiento = 'Inscrito';
                    $descripcion->colorEstatusSeguimiento  = 'text-success';
                }
                if(intval($descripcion->estatusSeguimiento) === 3){
                    $descripcion->nombreEstatusSeguimiento = 'No Interesado';
                    $descripcion->colorEstatusSeguimiento  = 'text-danger';
                }
                if(intval($descripcion->estatusSeguimiento) === 4){
                    $descripcion->nombreEstatusSeguimiento = 'Proximo Calendario';
                    $descripcion->colorEstatusSeguimiento  = 'text-purple';
                }


                if(intval($descripcion->tipo) === 2){
                    $descripcion->caducidad = formatearFecha($descripcion->caducidad);
                    $descripcion->descripcionDescuento = (intval($descripcion->tipoDescuento) === 1) ? 
                    $descripcion->descuento."%" : '$'.number_format($descripcion->descuento, 2, '.', ',');
                }

                $usuario = Usuario::find($descripcion->idUsuario);
                $empleado = Empleado::find($usuario->idEmpleado);
                $descripcion->usuario = $empleado->nombre.' '.$empleado->apellidoPaterno.' '.$empleado->apellidoMaterno;

                if(intval($descripcion->idCita) > 0){
                    $cita = Cita::find($descripcion->idCita);
                    if(intval($cita->estatus) === 1)
                        $descripcion->estatusCita = 'En Proceso';
                    if(intval($cita->estatus) === 2)
                        $descripcion->estatusCita = 'Finalizada';
                    if(intval($cita->estatus) === 3)
                        $descripcion->estatusCita = 'Cancelada';
                    if(intval($cita->estatus) === 4)
                        $descripcion->estatusCita = 'No Asistio';

                    $descripcion->mostrarDatosCita = (intval($cita->estatus) === 1) ? true : false;
                    $descripcion->motivoCita = $cita->motivo;
                    $descripcion->plantelCita = Sucursale::find($cita->idSucursal)->nombre;
                }
                $lista[] = $descripcion;
            }

            $sexos = Sexo::where('eliminado', '=', 0)->get();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $cursos = Curso::where('eliminado', '=', 0)->get();
            $sedes = Sede::where('eliminado', '=', 0)->get();
            $turnos = Turno::where('eliminado', '=', 0)->get();
            $selectHorarios = "SELECT CONCAT(inicio, ' - ', fin) as nombre, id FROM horarios WHERE eliminado = 0";
            $horarios = DB::select($selectHorarios, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $sedessucursales = Sedesucursale::where('eliminado', '=', 0)->get();
            $grupos = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
            join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select(
                'grupos.id as id',
                'altacursos.idCalendario',
                'altacursos.idNivel',
                'altacursos.idSubnivel',
                'altacursos.idCategoria',
                'altacursos.idModalidad',
                'altacursos.idSede',
                'altacursos.idCurso',
                'altacursos.inicio',
                'altacursos.fin',
                'altacursos.limitePago',
                'altacursos.precio',
                'cursos.nombre as curso',
                'grupos.idHorario', 
                'grupos.idTurno')->
            where('calendarios.fin', '>', $hoy)->get();
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();
            $tipoEscuelas = Tipoescuela::where('eliminado', '=', 0)->get();
            $escuelas = Escuela::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $centrosUniversitarios = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $carreras = Carrera::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select('carreras.*')->
            where('calendarios.fin', '>', $hoy)->
            where('carreras.eliminado', '=', 0)->get();
            $mediosContacto = Medioscontacto::where('eliminado', '=', 0)->get();
            $mediosPublicitarios = Mediospublicitario::where('eliminado', '=', 0)->get();
            $viasPublicitarias = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $motivosInscripcion = Motivosinscripcione::where('eliminado', '=', 0)->get();
            $motivosBachillerato = Motivosbachillerato::where('eliminado', '=', 0)->get();
            $campanias = Campania::where('eliminado', '=', 0)->get();
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();
            $metodosPago = Metodospago::where('eliminado', '=', 0)->get();
            $formasPago = Formaspago::where('eliminado', '=', 0)->get();
            $bancos = Banco::where('eliminado', '=', 0)->get();
            $cuentas = Cuenta::where('eliminado', '=', 0)->get();
            $conceptosAbonos = Conceptosabono::where('eliminado', '=', 0)->get();
            $conceptosCargos = Conceptoscargo::where('eliminado', '=', 0)->get();
            $conceptosDescuentos = Conceptosdescuento::where('eliminado', '=', 0)->get();
            $tiposPago = Tipopago::where('eliminado', '=', 0)->get();

            $listas['sexos'] = $sexos;
            $listas['calendarios'] = $calendarios;
            $listas['niveles'] = $niveles;
            $listas['subniveles'] = $subniveles;
            $listas['categorias'] = $categorias;
            $listas['modalidades'] = $modalidades;
            $listas['cursos'] = $cursos;
            $listas['sedes'] = $sedes;
            $listas['turnos'] = $turnos;
            $listas['horarios'] = $horarios;
            $listas['sucursales'] = $sucursales;
            $listas['sedesucursales'] = $sedessucursales;
            $listas['grupos'] = $grupos;
            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            $listas['escuelas'] = $escuelas;
            $listas['tipoEscuelas'] = $tipoEscuelas;
            $listas['universidades'] = $universidades;
            $listas['centrosUniversitarios'] = $centrosUniversitarios;
            $listas['carreras'] = $carreras;
            $listas['mediosContacto'] = $mediosContacto;
            $listas['mediosPublicitarios'] = $mediosPublicitarios;
            $listas['viasPublicitarias'] = $viasPublicitarias;
            $listas['motivosInscripcion'] = $motivosInscripcion;
            $listas['motivosBachillerato'] = $motivosBachillerato;
            $listas['campanias'] = $campanias;
            $listas['empresasCursos'] = $empresasCursos;
            $listas['metodosPago'] = $metodosPago;
            $listas['formasPago'] = $formasPago;
            $listas['bancos'] = $bancos;
            $listas['cuentas'] = $cuentas;
            $listas['conceptosAbonos'] = $conceptosAbonos;
            $listas['conceptosCargos'] = $conceptosCargos;
            $listas['conceptosDescuentos'] = $conceptosDescuentos;
            $listas['tiposPago'] = $tiposPago;
            $listas['convenios'] = Empresaconvenio::where('eliminado', '=', 0)->where('activo', '=', 1)->get();

            $respuesta['listas'] = $listas;
            $respuesta['prospecto'] = $prospecto;
            $respuesta['seguimiento'] = $seguimiento;
            $respuesta['descripciones'] = $lista;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarDescripcionSeguimiento(Request $request){
        try {
            /*if(intval($request['tipo']) === 2){
                $existe = Seguimientodescripcione::where('caducidad', '>=', 'NOW()')->
                                                   where('tipo', '=', 2)->get();
                if(count($existe) > 0){
                    return response()->json('Ya existe un porcentaje almacenado y aun no ha caducado', 400);
                }
            }*/
            $seguimiento = Seguimientodescripcione::create([
                'idUsuario' => $request['idUsuario'],
                'idSeguimiento' => $request['idSeguimiento'],
                'comentario' => $request['comentario'],
                'fecha' => $request['fecha'],
                'tipo' => $request['tipo'],
                'medio' => (intval($request['tipo']) === 1) ? $request['medio'] : 0,
                'descuento' => (intval($request['tipo']) === 2) ? $request['descuento'] : 0,
                'tipoDescuento' => (intval($request['tipo']) === 2) ? $request['tipoDescuento'] : 0,
                'conceptoDescuento' => (intval($request['tipo']) === 2) ? $request['conceptoDescuento'] : 0,
                'caducidad' => (intval($request['tipo']) === 2) ? $request['caducidad'] : null,
                'estatusSeguimiento' => $request['estatusSeguimiento'],
                'idCita' => 0,
                'activo' => 1,
                'eliminado' => 0
            ]);    
            return response()->json($seguimiento, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarEstatusSeguimiento(Request $request){
        try {
            $seguimiento = Seguimiento::find($request['idSeguimiento']);
            if(intval($seguimiento->estatus) !== intval($request['estatus'])){
                $estatusFinal = '';
                if(intval($request['estatus']) === 0)
                    $estatusFinal = 'Prospecto';
                if(intval($request['estatus']) === 1)
                    $estatusFinal = 'Interesado';
                if(intval($request['estatus']) === 2)
                    $estatusFinal = 'Inscrito';
                if(intval($request['estatus']) === 3)
                    $estatusFinal = 'No Interesado';
                if(intval($request['estatus']) === 4)
                    $estatusFinal = 'Proximo Calendario';

                $descripcion = Seguimientodescripcione::create([
                    'idUsuario' => $request['idUsuario'],
                    'idSeguimiento' => $seguimiento->id,
                    'comentario' => 'Se cambio a estatus '.$estatusFinal ,
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 7,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => $seguimiento->estatus,
                    'activo' => 1,
                    'eliminado' => 0
                ]); 
            }
            $seguimiento->estatus = $request['estatus'];
            $seguimiento->save();
            return response()->json($seguimiento, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarCita(Request $request){
        try {
            DB::beginTransaction();
            $seguimiento = Seguimiento::find($request['idSeguimiento']);
            $existe = Cita::where('idSeguimiento', '=', $request['idSeguimiento'])->
                            where('fecha', '>=', Carbon::now())->
                            where('estatus', '=', 1)->get();
            if(count($existe) > 0){
                return response()->json('Este prospecto tiene una cita pendiente en el plantel '.Sucursale::find($existe[0]->idSucursal)->nombre, 400);
            }
            $cita = Cita::create([
                'idUsuario' => $request['idUsuario'],
                'idSeguimiento' => $request['idSeguimiento'],
                'idSucursal' => $request['idSucursal'],
                'motivo' => $request['motivo'],
                'fecha' => $request['fecha'],
                'estatus' => 1,
                'eliminado' => 0,
                'activo' => 1
            ]);

            $descripcion = Seguimientodescripcione::create([
                'idUsuario' => $request['idUsuario'],
                'idSeguimiento' => $request['idSeguimiento'],
                'comentario' => 'Se creo la cita para el dia '.formatearFecha($cita->fecha),
                'fecha' => Carbon::now(),
                'tipo' => 1,
                'medio' => 8,
                'descuento' => 0,
                'tipoDescuento' => 0,
                'conceptoDescuento' => 0,
                'caducidad' => null,
                'estatusSeguimiento' => $seguimiento->estatus,
                'idCita' => $cita->id,
                'activo' => 1,
                'eliminado' => 0
            ]);
            DB::commit();
            return response()->json($cita, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarEstatusCita(Request $request){
        try {
            $cita = Cita::find($request['idCita']);
            $seguimiento = Seguimiento::find($request['idSeguimiento']);
            if(intval($request['idSucursal']) !== intval($cita->idSucursal) && intval($request['idSucursal']) !== 1){
                return response()->json('No puedes modificar la cita ya que no esta agendada a tu sucursal', 400);
            }
            if(intval($request['estatus']) === 2){
                $descripcion = Seguimientodescripcione::create([
                    'idUsuario' => $request['idUsuario'],
                    'idSeguimiento' => $request['idSeguimiento'],
                    'comentario' => 'El alumno asistio a la cita del dia '.formatearFecha($cita->fecha),
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 9,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => $seguimiento->estatus,
                    'idCita' => $cita->id,
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }else if(intval($request['estatus']) === 3){
                $descripcion = Seguimientodescripcione::create([
                    'idUsuario' => $request['idUsuario'],
                    'idSeguimiento' => $request['idSeguimiento'],
                    'comentario' => 'Cancelo la cita del dia '.formatearFecha($cita->fecha),
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 10,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => $seguimiento->estatus,
                    'idCita' => $cita->id,
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }else{
                $fechaActual = Carbon::now();
                if(mayor($cita->fecha, $fechaActual)){
                    return response()->json('Aun no es el dia de la cita del alumno', 400);
                }
                $descripcion = Seguimientodescripcione::create([
                    'idUsuario' => $request['idUsuario'],
                    'idSeguimiento' => $request['idSeguimiento'],
                    'comentario' => 'El alumno no asistio a la cita del dia '.formatearFecha($cita->fecha),
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 11,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => $seguimiento->estatus,
                    'idCita' => $cita->id,
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }
            $cita->estatus = $request['estatus'];
            $cita->save();
            return response()->json($cita, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrarCitas(Request $request){
        try {
            $sucursal = $request['idSucursal'];
            $consulta = "SELECT CONCAT(p.nombre, ' ', p.apellidoPaterno, ' ',p.apellidoMaterno) AS nombre, c.motivo, p.celular,
                         c.fecha, c.idSeguimiento
                         FROM citas c, prospectos p, seguimientos s
                         WHERE c.idSeguimiento = s.id AND p.id = s.idProspecto AND c.estatus = 1 AND c.idSucursal = $sucursal";
            $registros = DB::select($consulta, array());
            return response()->json($registros, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function esVentas(Request $request){
        try {
            $usuario = Usuario::find($request['usuarioID']);
            $empleado = Empleado::find($usuario->idEmpleado);
            $respuesta = array();
            if(intval($empleado->idDepartamento) === 6){
                $respuesta['es'] = true;
            }else{
                $respuesta['es'] = false;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscarFicha(Request $request){
        try {
            $folio = $request['folio'];
            $consulta = "SELECT f.id, CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) as alumno, c.icono as icono, c.nombre as curso FROM fichas f, grupos g, altacursos ac, cursos c, alumnos a WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCurso = c.id AND a.id = f.idAlumno AND f.folio = '$folio'";
            $fichas = DB::select($consulta, array());
            if(count($fichas) < 1){
                return response()->json('No existe ficha con el folio '.$request['folio'], 400);
            }else{
                return response()->json($fichas, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function confirmarPassword(Request $request){
        try {
            $usuario = Usuario::find(15);
            $request['usuario'] = $usuario->usuario;
            $this->validate($request, [
                'usuario'    => 'required',
            ]);
            try {
                if ( !$token = $this->jwt->attempt($request->only('usuario', 'password'))) {
                    return response()->json('Contraseña Incorrecta', 400);
                }
            } catch (Exception $e){
                return response()->json($e);    
            }
            return response()->json('Sesion iniciada correctamente');
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function asignarFicha(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            $ficha->idUsuarioInformacion = $request['idUsuario'];
            $ficha->save();

            $existe = Seguimiento::where('idFicha', '=', $request['idFicha'])->get();
            if(count($existe) > 0){
                return response()->json('La ficha seleccionada ya esta asignada a un seguimiento');
            }

            $seguimiento = Seguimiento::find($request['idSeguimiento']);
            $seguimiento->estatus = 2;
            $seguimiento->idFicha = $request['idFicha'];
            $seguimiento->save();
            
            $descripcion = Seguimientodescripcione::create([
                'idUsuario' => $request['idUsuario'],
                'idSeguimiento' => $seguimiento->id,
                'comentario' => 'Alumno Inscrito con la ficha '.$ficha->folio,
                'fecha' => Carbon::now(),
                'tipo' => 1,
                'medio' => 12,
                'descuento' => 0,
                'tipoDescuento' => 0,
                'conceptoDescuento' => 0,
                'caducidad' => null,
                'estatusSeguimiento' => 2,
                'idCita' => 0,
                'activo' => 1,
                'eliminado' => 0
            ]); 
            return response()->json($seguimiento, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}