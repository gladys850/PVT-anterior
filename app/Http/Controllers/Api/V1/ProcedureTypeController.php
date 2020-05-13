<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProcedureType;
use App\RoleSequence;
use App\Role;
use App\Http\Requests\RoleSequenceForm;
use Util;

/** @group Tipos de trámites
* Trámites agrupados por tipo de acuerdo a un filtro de modalidad
*/
class ProcedureTypeController extends Controller
{
    /**
    * Lista de trámites
    * Devuelve el listado con los datos paginados
    * @queryParam module_id Filtro de ID del módulo. Example: 6
    * @queryParam sortBy Vector de ordenamiento. Example: [name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 10
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/procedure_type/index.200.json
     */
    public function index(Request $request)
    {
        $filter = $request->has('module_id') ? ['module_id' => $request->module_id] : [];
        return Util::search_sort(new ProcedureType(), $request, $filter);
    }

    /**
    * Listado de destinos de préstamo
    * Obtiene la lista de destinos de préstamos por modalidad
    * @urlParam procedure_type required ID de la modalidad. Example: 9
    * @authenticated
    * @responseFile responses/procedure_type/get_loan_destinies.200.json
    */
    public function get_loan_destinies(ProcedureType $procedure_type)
    {
        return $procedure_type->loan_destinies;
    }

    /**
    * Actualizar destinos de préstamo
    * Modifica la lista de destinos de préstamos por modalidad
    * @urlParam procedure_type required ID de la modalidad. Example: 9
    * @bodyParam destinies array required Listado de IDs de destinos de préstamo.
    * @bodyParam destinies[*] integer required ID de destino. Example: 2
    * @authenticated
    * @responseFile responses/procedure_type/get_loan_destinies.200.json
    */
    public function set_loan_destinies(Request $request, ProcedureType $procedure_type)
    {
        $request->validate([
            'destinies' => 'required|array|min:1',
            'destinies.*' => 'exists:loan_destinies,id'
        ]);
        $procedure_type->loan_destinies()->sync($request->destinies);
        return $procedure_type->loan_destinies;
    }

    /**
    * Obtener flujo de trabajo
    * Obtiene la lista de roles para derivación de trámites de una modalidad
    * @urlParam procedure_type required ID de la modalidad. Example: 9
    * @authenticated
    * @responseFile responses/procedure_type/get_flow.200.json
    */
    public function get_flow(ProcedureType $procedure_type)
    {
        return $procedure_type->workflow;
    }

    /**
    * Actualizar flujo de trabajo
    * Actualiza la secuencia de roles para derivación de trámites de una modalidad
    * @urlParam procedure_type required ID de la modalidad. Example: 9
    * @bodyParam workflow array required Listado de secuencias de derivación.
    * @bodyParam workflow[*].role_id integer required Área desde la cual se derivará. Example: 81
    * @bodyParam workflow[*].next_role_id integer required Área a la cual se derivará. Example: 73
    * @authenticated
    * @responseFile responses/procedure_type/get_flow.200.json
    */
    public function set_flow(RoleSequenceForm $request, ProcedureType $procedure_type)
    {
        $request = collect($request->workflow)->map(function($item) use ($procedure_type) {
            if (Role::find($item['role_id'])->sequence_number >= Role::find($item['next_role_id'])->sequence_number) abort(409, 'El rol destino ser superior al de origen');
            $item['procedure_type_id'] = $procedure_type->id;
            return $item;
        })->values()->toArray();
        foreach ($request as $key => $sequence) {
            foreach ($request as $i => $compare) {
                if ($key != $i) {
                    if ($sequence['role_id'] == $compare['role_id'] && $sequence['next_role_id'] == $compare['next_role_id']) abort(409, 'No se pueden guardar secuencias duplicadas');
                }
            }
        }
        RoleSequence::whereProcedureTypeId($procedure_type->id)->delete();
        foreach ($request as $sequence) {
            RoleSequence::create($sequence);
        }
        return $procedure_type->workflow;
    }
}
