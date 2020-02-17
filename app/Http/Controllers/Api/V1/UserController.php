<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserForm;
use App\User;
use Ldap;
use Util;

/** @group Usuarios
* Datos de los usuarios y métodos para obtener y establecer sus relaciones
*/
class UserController extends Controller
{
    private $db_users = ['admin', 'asistente'];

    /**
    * Lista de usuarios
    * Devuelve el listado con los datos paginados
    * @queryParam active Usuarios activos(1) o inactivos(0). Example: 1
    * @queryParam search Parámetro de búsqueda. Example: TORRE
    * @queryParam sortBy Vector de ordenamiento. Example: [last_name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *     "current_page": 1,
    *     "data": [
    *         {
    *             "id": 138,
    *             "city_id": 4,
    *             "first_name": "ROLANDO",
    *             "last_name": "FERNANDEZ SALAZAR",
    *             "username": "rfernandez",
    *             "created_at": "2019-10-17 17:46:39",
    *             "updated_at": "2019-10-17 17:46:39",
    *             "remember_token": "H2C9sw0YQ8ljNEoGZbvSzxHHnu2exY3zpGzgYOOiXT01Kx0KTcyDApjbjP1Y",
    *             "position": "CONSULTOR INDIVIDUAL DE LINEA - CALIFICADOR DEL COMPLEMENTO ECONOMICO",
    *             "is_commission": null,
    *             "phone": "65648571",
    *             "active": true
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/user?page=1",
    *     "from": 1,
    *     "last_page": 7,
    *     "last_page_url": "http://127.0.0.1/api/v1/user?page=7",
    *     "next_page_url": "http://127.0.0.1/api/v1/user?page=2",
    *     "path": "http://127.0.0.1/api/v1/user",
    *     "per_page": "8",
    *     "prev_page_url": null,
    *     "to": 8,
    *     "total": 49
    * }
    */
    public function index(Request $request)
    {
        $filter = $request->has('active') ? [
            'active' => Util::get_bool($request->active),
            'username' => ['!=', Auth::user()->username],
            'username' => ['!=', 'admin']
        ] : [];
        return Util::search_sort(new User(), $request, $filter);
    }

    /**
    * Nuevo usuario
    * Inserta nuevo usuario
    * @bodyParam first_name string required Nombres. Example: JUAN
    * @bodyParam last_name string required Apellidos. Example: TORRES
    * @bodyParam username string required Nombre de usuario. Example: jtorres
    * @bodyParam password string required Contraseña: as$32rW!%V
    * @bodyParam position string Cargo. Example: PROFESIONAL DE COBRANZAS
    * @authenticated
    * @response
    * {
    *     "id": 138,
    *     "city_id": 4,
    *     "first_name": "ROLANDO",
    *     "last_name": "FERNANDEZ SALAZAR",
    *     "username": "rfernandez",
    *     "created_at": "2019-10-17 17:46:39",
    *     "updated_at": "2019-10-17 17:46:39",
    *     "remember_token": "",
    *     "position": "CONSULTOR INDIVIDUAL DE LINEA - CALIFICADOR DEL COMPLEMENTO ECONOMICO",
    *     "is_commission": null,
    *     "phone": "",
    *     "active": true
    * }
    */
    public function store(UserForm $request)
    {
        if (env("LDAP_AUTHENTICATION")) {
            $ldap = new Ldap();
            if (is_null($ldap->get_entry($request->username, 'uid'))) {
                abort(404);
            }
        }
        return User::create($request->all());
    }

    /**
    * Detalle de usuario
    * @urlParam id required ID de usuario. Example: 138
    * @authenticated
    * @response
    * {
    *     "id": 138,
    *     "city_id": 4,
    *     "first_name": "ROLANDO",
    *     "last_name": "FERNANDEZ SALAZAR",
    *     "username": "rfernandez",
    *     "created_at": "2019-10-17 17:46:39",
    *     "updated_at": "2019-10-17 17:46:39",
    *     "remember_token": "",
    *     "position": "CONSULTOR INDIVIDUAL DE LINEA - CALIFICADOR DEL COMPLEMENTO ECONOMICO",
    *     "is_commission": null,
    *     "phone": "",
    *     "active": true
    * }
    */
    public function show($id)
    {
        $user = User::findOrFail($id);
        if (Auth::user()->id == $id || Auth::user()->can('show-user')) {
            return $user;
        } else {
            abort(401);
        }
    }

    /**
    * Actualizar usuario
    * @urlParam id required ID de usuario. Example: 138
    * @bodyParam first_name string Nombres. Example: JUAN
    * @bodyParam last_name string Apellidos. Example: TORRES
    * @bodyParam username string Nombre de usuario. Example: jtorres
    * @bodyParam password string Contraseña: as$32rW!%V
    * @bodyParam position string Cargo. Example: PROFESIONAL DE COBRANZAS
    * @authenticated
    * @response
    * {
    *     "id": 138,
    *     "city_id": 4,
    *     "first_name": "ROLANDO",
    *     "last_name": "FERNANDEZ SALAZAR",
    *     "username": "rfernandez",
    *     "created_at": "2019-10-17 17:46:39",
    *     "updated_at": "2019-10-17 17:46:39",
    *     "remember_token": "",
    *     "position": "CONSULTOR INDIVIDUAL DE LINEA - CALIFICADOR DEL COMPLEMENTO ECONOMICO",
    *     "is_commission": null,
    *     "phone": "",
    *     "active": true
    * }
    */
    public function update(UserForm $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->has('password') && !env("LDAP_AUTHENTICATION")) {
            if ($request->has('old_password')) {
                if (!(Auth::user()->id == $id && Hash::check($request->input('old_password'), $user->password))) {
                    return response()->json([
                        'message' => 'Invalid',
                        'errors' => [
                            'type' => ['Contraseña anterior incorrecta'],
                        ],
                    ], 409);
                }
            } else {
                unset($request['password']);
            }
        }
        $user->fill($request->all());
        $user->save();
        return $user;
    }

    /**
    * Eliminar usuario
    * Elimina un usuario solo en caso de que no tenga acciones registradas
    * @urlParam id required ID de usuario. Example: 138
    * @authenticated
    * @response
    * {
    *     "id": 138,
    *     "city_id": 4,
    *     "first_name": "ROLANDO",
    *     "last_name": "FERNANDEZ SALAZAR",
    *     "username": "rfernandez",
    *     "created_at": "2019-10-17 17:46:39",
    *     "updated_at": "2019-10-17 17:46:39",
    *     "remember_token": "",
    *     "position": "CONSULTOR INDIVIDUAL DE LINEA - CALIFICADOR DEL COMPLEMENTO ECONOMICO",
    *     "is_commission": null,
    *     "phone": "",
    *     "active": true
    * }
    */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $has_actions = $user->has_actions();
        if ($user->has_actions() || in_array($user->username, $this->db_users)) {
            return response()->json([
				'message' => 'Forbidden',
				'errors' => [
					'type' => ['El usuario aún tiene acciones registradas'],
				]
			], 403);
        } else {
            $user->records()->delete();
            $user->delete();
            return $user;
        }
    }

    /**
    * Obtener permisos de usuario
    * Devuelve un listado con los IDs de los permisos asignados al usuario
    * @urlParam id required ID de usuario. Example: 69
    * @authenticated
    * @response
    * [
    *     12
    * ]
    */
    public function get_permissions($id)
    {
        $user = User::findOrFail($id);
        return $user->allPermissions()->pluck('id');
    }

    /**
    * Obtener roles de usuario
    * Devuelve un listado con los IDs de los roles asignados al usuario
    * @urlParam id required ID de usuario. Example: 69
    * @authenticated
    * @response
    * [
    *     19,
    *     53,
    *     65
    * ]
    */
    public function get_roles($id)
    {
        $user = User::findOrFail($id);
        return $user->roles()->pluck('id');
    }

    /**
    * Establecer roles de usuario
    * Asignar roles a un usuario
    * @urlParam id required ID de usuario. Example: 69
    * @bodyParam roles array required Vector con los objetos de roles. Example: [{id:37},{id:42},{id:7}]
    * @authenticated
    * @response
    * [
    *     37,
    *     42,
    *     7
    * ]
    */
    public function set_roles(Request $request, $id)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*.id' => 'required|integer|exists:roles,id'
        ]);
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles);
        return $user->roles()->pluck('id');
    }

    /**
    * Usuarios no registrados
    * Devuelve un listado con datos de los usuarios del Active Directory que no se encuentran registrados en el sistema
    * @authenticated
    * @response
    * [
    *     {
    *         "cn": "JOHNNY ALAVE",
    *         "sn": "ALAVE GUTIERREZ",
    *         "employeenumber": "2",
    *         "givenname": "JOHNNY JUNIOR",
    *         "mail": "jalave@muserpol.gob.bo",
    *         "title": "Técnico de Atención al Afiliado II",
    *         "uid": "jalave"
    *     }, {}
    * ]
    */
    public function unregistered_users()
    {
        $ldap = new Ldap();
        $unregistered_users = collect($ldap->get_entries())->pluck('uid')->diff(User::get()->pluck('username')->all());
        $items = [];
        foreach($unregistered_users as $user) {
            array_push($items, $ldap->get_entry($user, 'uid'));
        }
        return response()->json(collect($items)->sortBy('sn')->values());
    }

    /**
    * Sincronizar usuarios
    * Cambia el estado de activo a inactivo para los usuarios del sistema que no se encuentren en el Active Directory
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 122,
    *         "city_id": 4,
    *         "first_name": "Juan Carlos",
    *         "last_name": "Barrios Gutierrez",
    *         "username": "jbarrios",
    *         "created_at": "2019-03-25 16:26:24",
    *         "updated_at": "2020-01-20 08:42:16",
    *         "remember_token": "UpyA8Iju5ij5WUdY2QDyrOsmFERDSdhlk34ekMkCK2T7lqr44bCR7JS6euW5",
    *         "position": "Encargado de Complemento Economico",
    *         "is_commission": false,
    *         "phone": null,
    *         "active": true
    *     }, {}
    * ]
    */
    public function synchronize_users()
    {
        $ldap = new Ldap();
        $discharged_users = collect(User::where(function($query) {
            foreach($this->db_users as $user) {
                $query->where('username', '!=', $user);
            }
        })->whereActive(true)->get()->pluck('username')->all())->diff(collect($ldap->get_entries())->pluck('uid'));
        $items = [];
        foreach($discharged_users as $user) {
            array_push($items, User::whereUsername($user)->first());
        }
        return response()->json(collect($items)->sortBy('last_name')->values());
    }
}
