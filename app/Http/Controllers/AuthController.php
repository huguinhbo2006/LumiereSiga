<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use App\Usuario;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'usuario'    => 'required',
        ]);
        try {
            if ( !$token = $this->jwt->attempt($request->only('usuario', 'password'))) {
                return response()->json('Usuario no encontrado', 400);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json('Sesion expirada', 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json('Sesion invalida', 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent' => $e->getMessage()], 500);

        } catch (Exception $e){
            return response()->json($e);    
        }
        
        return response()->json(compact('token'));
    }
}