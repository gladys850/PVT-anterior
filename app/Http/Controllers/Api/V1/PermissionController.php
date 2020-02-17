<?php

namespace App\Http\Controllers\Api\V1;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/** @group Permisos
* Datos de los permisos disponibles en el sistema
*/
class PermissionController extends Controller
{
    /**
    * Lista de permisos
    * Devuelve el listado de los permisos disponibles en el sistema
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 596,
    *         "operation_id": null,
    *         "action_id": null,
    *         "created_at": "2020-02-13 16:26:58",
    *         "updated_at": "2020-02-13 16:26:58",
    *         "name": "create-address",
    *         "display_name": "Crear dirección"
    *     }, {}
    * ]
    */
    public function index()
    {
        return Permission::where('name', '!=', null)->orderBy('name')->get();
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Permission  $permission
    * @return \Illuminate\Http\Response
    */
    public function show(Permission $permission)
    {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Permission  $permission
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Permission  $permission
    * @return \Illuminate\Http\Response
    */
    public function destroy(Permission $permission)
    {
        //
    }
}
