<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LoanDestiny;
use Util;

/** @group Préstamos
* Datos de los destinos de préstamos
*/
class LoanDestinyController extends Controller
{
    /**
    * Lista de destinos de Préstamos
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: salud
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [0]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *   "current_page": 1,
    *   "data": [
    *       {
    *           "id": 1,
    *           "procedure_type_id": 9,
    *           "name": "Salud",
    *           "description": "Salud",
    *           "created_at": null,
    *           "updated_at": null
    *       },
    *       {
    *           "id": 2,
    *           "procedure_type_id": 9,
    *           "name": "Consumo",
    *           "description": "Consumo",
    *           "created_at": null,
    *           "updated_at": null
    *       }
    *   ],
    *   "first_page_url": "http://127.0.0.1/api/v1/destiny?page=1",
    *   "from": 1,
    *   "last_page": 1,
    *   "last_page_url": "http://127.0.0.1/api/v1/destiny?page=1",
    *   "next_page_url": null,
    *   "path": "http://127.0.0.1/api/v1/destiny",
    *   "per_page": 10,
    *   "prev_page_url": null,
    *   "to": 2,
    *   "total": 2
    * }
    */
    public function index(Request $request)
    {
        return Util::search_sort(new LoanDestiny(), $request);
    }

    /**
    * Nuevo destino de Préstamo
    * Inserta nuevo destino de préstamo
    * @bodyParam procedure_type_id integer required ID de la modalidad de Préstamo. Example:9
    * @bodyParam name string required destino de Préstamo. Example: "Salud"
    * @bodyParam description string descripción de destino de Préstamo. Example: "Salud Familiar"
    * @authenticated
    * @response
    * {
    *    "procedure_type_id": 9,
    *    "name": "Salud",
    *    "description": "Salud familiar",
    *    "updated_at": "2020-03-11 10:39:56",
    *    "created_at": "2020-03-11 10:39:56",
    *    "id": 6
    * }
    */
    public function store(Request $request)
    {
        return LoanDestiny::create($request->all());
    }

    /**
    * Detalle de destino de Préstamo
    * Devuelve el detalle de un destino de préstamo mediante su ID
    * @urlParam destiny required ID de destino de préstamo. Example: 6
    * @response
    * {
    *   "id": 6,
    *   "procedure_type_id": 9,
    *   "name": "Salud",
    *   "description": "Salud familiar",
    *   "created_at": "2020-03-11 10:39:56",
    *   "updated_at": "2020-03-11 10:39:56"
    * }
    */
    public function show($id)
    {
        return LoanDestiny::findOrFail($id);
    }

    /**
    * Actualizar destino de Préstamo
    * Actualizar datos principales destino de préstamo
    * @urlParam id required ID de destino de Préstamo. Example: 1
    * @bodyParam procedure_type_id integer required ID de la modalidad de Préstamo. Example:9
    * @bodyParam name string required destino de Préstamo. Example: "Salud"
    * @bodyParam description string descripción de destino de Préstamo. Example: "Salud General"
    * @authenticated
    * @response
    * {
    *    "id": 6,
    *    "procedure_type_id": 9,
    *    "name": "Salud",
    *    "description": "Salud General",
    *    "created_at": "2020-03-11 10:39:56",
    *    "updated_at": "2020-03-11 10:48:45"
    * }
    */
    public function update(Request $request, $id)
    {
        $destiny = LoanDestiny::findOrFail($id);
        $destiny->fill($request->all());
        $destiny->save();
        return  $destiny;
    }

    /**
    * Eliminar un destino de Préstamo
    * @urlParam id required ID de destino de Préstamo. Example: 6
    * @authenticated
    * @response
    * {
    *   "id": 6,
    *   "procedure_type_id": 9,
    *   "name": "Salud",
    *   "description": "Salud General",
    *   "created_at": "2020-03-11 10:39:56",
    *   "updated_at": "2020-03-11 10:48:45"
    * }
    */
    public function destroy($id)
    {
        $destiny = LoanDestiny::findOrFail($id);
        $destiny->delete();
        return $destiny;
    }
}
