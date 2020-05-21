<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StatisticForm;
use App\Module;
use App\Loan;

/** @group Estadísticas
* Estadísticas de los trámites almacenados en la base de datos
*/
class StatisticController extends Controller
{
    public $filters;

    public function __construct()
    {
        $this->filters = [
            'prestamos' => [
                'role_id' => [
                    'display_name' => 'Número de trámites por área',
                    'method' => 'loans_by_role'
                ]
            ]
        ];
    }

    public function get_filters(){
        return $this->filters;
    }

    /**
    * Datos estadísticos
    * Devuelve los datos estatdísticos de acuerdo al filtro seleccionado
    * @queryParam module Nombre de módulo para obtener las estadísticas. Example: prestamos
    * @queryParam filter Filtro para consultar en la base de datos. Example: role_id
    * @authenticated
    * @responseFile responses/statistic/index.200.json
    */
    public function index(StatisticForm $request)
    {
        $module = Module::whereName($request->module)->first();
        return $this->{$this->filters[$request->module][$request->filter]['method']}($module);
    }

    /**
    * Filtros disponibles
    * Devuelve el listado de los filtros disponibles para el módulo seleccionado
    * @urlParam module required Nombre de módulo para obtener los filtros disponibles. Example: prestamos
    * @authenticated
    * @responseFile responses/statistic/show.200.json
    */
    public function show($module)
    {
        if (!array_key_exists($module, $this->get_filters())) abort(404, 'Módulo inexistente');
        return [$this->get_filters()[$module]];
    }

    public function loans_by_role(Module $module)
    {
        $data = [];
        foreach ($module->roles()->whereNotNull('sequence_number')->orderBy('sequence_number')->orderBy('display_name')->get() as $role) {
            $data[] = [
                'role_id' => $role->id,
                'data' => [
                    'received' => Loan::whereRoleId($role->id)->whereValidated(true)->count(),
                    'validated' => Loan::whereRoleId($role->id)->whereValidated(false)->count(),
                    'trashed' => Loan::whereRoleId($role->id)->onlyTrashed()->count()
                ]
            ];
        }
        return $data;
    }
}