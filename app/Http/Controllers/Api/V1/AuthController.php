<?php

namespace App\Http\Controllers\Api\V1;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Ldap;

/** @resource Authenticate
 *
 * Resource to authenticate via username/password credentials
 */

class AuthController extends Controller
{
   /**
   * Get the authenticated User.
   *
   * Login, return a JsonWebToken to request as "Bearer" Authorization header
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    return response()->json(Auth::user());
  }

  /**
   * Get a JWT via given credentials.
   *
   * Login, return a JsonWebToken to request as "Bearer" Authorization header
   *
   * @return \Illuminate\Http\JsonResponse
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
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    Auth::logout();
    return response()->json([
      'message' => 'Logged out successfully',
    ], 201);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
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
