<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ProcedureType;
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
    * Listado de destinos de préstamo por modalidad
    * Obtiene la lista de destinos de préstamos por modalidad
    * @urlParam procedure_type required ID de la modalidad. Example: 9
    * @authenticated
    * @responseFile responses/procedure_type/get_loan_destinies.200.json
    */
    public function get_loan_destinies(ProcedureType $procedure_type)
    {
        return $procedure_type->loan_destinies;
    }
}
