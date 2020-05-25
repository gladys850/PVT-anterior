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
                'role' => [
                    'display_name' => 'Número de trámites por área',
                    'method' => 'loans_by_role'
                ],
                'procedure_type' => [
                    'display_name' => 'Número de trámites por tipo trámite',
                    'method' => 'loans_by_procedure_type'
                ],
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
    * @queryParam filter Filtro para consultar en la base de datos. Example: role
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
                    'received' => Loan::whereRoleId($role->id)->whereValidated(false)->count(),
                    'validated' => Loan::whereRoleId($role->id)->whereValidated(true)->count(),
                    'trashed' => Loan::whereRoleId($role->id)->onlyTrashed()->count()
                ]
            ];
        }
        return $data;
    }

    public function loans_by_procedure_type(Module $module)
    {
        $data = [];
        foreach ($module->procedure_types()->orderBy('name')->get() as $key => $procedure_type) {
            $data[] = [
                'procedure_type_id' => $procedure_type->id,
                'total' => [
                    'received' => 0,
                    'validated' => 0,
                    'trashed' => 0
                ]
            ];
            foreach ($module->roles()->whereNotNull('sequence_number')->orderBy('sequence_number')->orderBy('display_name')->get() as $subkey => $role) {
                $data[$key]['data'][$subkey] = [
                    'role_id' => $role->id
                ];
                $values = [
                    Loan::whereRoleId($role->id)->whereHas('modality', function($q) use ($procedure_type) {
                        $q->whereProcedureTypeId($procedure_type->id);
                    })->whereValidated(false)->count(), //received
                    Loan::whereRoleId($role->id)->whereHas('modality', function($q) use ($procedure_type) {
                        $q->whereProcedureTypeId($procedure_type->id);
                    })->whereValidated(true)->count(), //validated
                    Loan::whereRoleId($role->id)->whereHas('modality', function($q) use ($procedure_type) {
                        $q->whereProcedureTypeId($procedure_type->id);
                    })->onlyTrashed()->count() //trashed
                ];
                $i = 0;
                foreach ($data[$key]['total'] as $total_key => $v) {
                    $data[$key]['total'][$total_key] += $values[$i];
                    $data[$key]['data'][$subkey]['data'][$total_key] = $values[$i];
                    $i++;
                }
            }
        }
        return $data;
    }
}