<?php

namespace App\Http\Controllers\Api\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Ldap;

/** @group Autenticación
* Abre el acceso a la aplicación mediante llaves JSON WebToken de tipo Bearer
*/

class AuthController extends Controller
{
    /**
    * Usuario autenticado
    * Devuelve el usuario actualmente autenticado
    * @response
    * {
    *     "id": 127,
    *     "city_id": 10,
    *     "first_name": "Administrador",
    *     "last_name": "Administrador",
    *     "username": "admin",
    *     "created_at": "2019-06-17 15:04:11",
    *     "updated_at": "2020-02-04 16:30:54",
    *     "remember_token": "Hc3j8...",
    *     "position": "Administrador",
    *     "is_commission": false,
    *     "phone": 65432101,
    *     "active": true
    * }
    * @authenticated
    */
    public function index()
    {
        return response()->json(Auth::user());
    }

    /**
    * Obtener token
    * El token servirá para consultar rutas protegidas
    * @bodyParam username string required Nombre de usuario. Example: admin
    * @bodyParam password string required Contraseña. Example: admin
    * @response
    * {
    *     "access_token": "a35fd...",
    *     "token_type": "bearer",
    *     "expires_in": 18000
    * }
    */
    public function store(AuthForm $request)
    {
        $token = false;
        if ($request->username == 'admin') {
            $token = Auth::attempt($request->all());
        } else {
            $user = User::whereUsername($request->username)->first();
            if ($user) {
                if (!$user->active) {
                    return response()->json([
                        'message' => 'No autorizado',
                        'errors' => (object)[
                            'username' => ['Usuario desactivado'],
                        ],
                    ], 401);
                } else {
                    if (!env("LDAP_AUTHENTICATION")) {
                        $token = Auth::attempt($request->all());
                    } else {
                        $ldap = new Ldap();
                        if ($ldap->verify_open_port()) {
                            if ($ldap->bind($request->username, $request->password)) {
                                if (!Hash::check($request->password, $user->password)) {
                                    $user->password = Hash::make($request->password);
                                    $user->save();
                                }
                                $token = Auth::login($user);
                            }
                        }
                    }
                }
            }
        }
        if ($token) {
            \Log::info("Usuario ".Auth::user()->username." autenticado desde la dirección ".request()->ip());
            return $token;
        }
        return response()->json([
            'message' => 'No autorizado',
            'errors' => (object)[
                'username' => ['Usuario o contraseña incorrectos'],
            ],
        ], 401);
    }

    /**
    * Cerrar sesión
    * El token se deshabilita
    * @response
    * {
    *     "message": "Logged out successfully"
    * }
    * @authenticated
    */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Logged out successfully',
        ], 201);
    }

    /**
    * Refrescar token
    * El token actual se deshabilita y se genera otro para alargando el tiempo de sesión
    * @response
    * {
    *     "access_token": "a35fd...",
    *     "token_type": "bearer",
    *     "expires_in": 18000
    * }
    * @authenticated
    */
    public function refresh()
    {
        return Auth::refresh();
    }

    public function guard()
    {
        return Auth::Guard('api');
    }
}
