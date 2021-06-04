<?php

namespace App\Http\Controllers\Api\V1;

use Util;
use App\LoanDestiny;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoanDestinyForm;

/** @group Destino Préstamo
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
    * @responseFile responses/loan_destiny/index.200.json
    */
    public function index(Request $request)
    {
        return Util::search_sort(new LoanDestiny(), $request);
    }

    /**
    * Nuevo destino de Préstamo
    * Inserta nuevo destino de préstamo
    * @bodyParam procedure_type_id integer required ID de la modalidad de Préstamo. Example: 9
    * @bodyParam name string required destino de Préstamo. Example: Salud
    * @bodyParam description string descripción de destino de Préstamo. Example: Salud Familiar
    * @authenticated
    * @responseFile responses/loan_destiny/store.200.json
    */
    public function store(LoanDestinyForm $request)
    {
        return LoanDestiny::create($request->all());
    }

    /**
    * Detalle de destino de Préstamo
    * Devuelve el detalle de un destino de préstamo mediante su ID
    * @urlParam loan_destiny required ID de destino de préstamo. Example: 6
    * @responseFile responses/loan_destiny/show.200.json
    * @response
    */
    public function show(LoanDestiny $loan_destiny)
    {
        return $loan_destiny;
    }

    /**
    * Actualizar destino de Préstamo
    * Actualizar datos principales destino de préstamo
    * @urlParam loan_destiny required ID de destino de Préstamo. Example: 1
    * @bodyParam procedure_type_id integer required ID de la modalidad de Préstamo. Example: 9
    * @bodyParam name string required destino de Préstamo. Example: Salud
    * @bodyParam description string descripción de destino de Préstamo. Example: Salud General
    * @authenticated
    * @responseFile responses/loan_destiny/update.200.json
    */
    public function update(LoanDestinyForm $request, LoanDestiny $loan_destiny)
    {
        $loan_destiny->fill($request->all());
        $loan_destiny->save();
        return  $loan_destiny;
    }

    /**
    * Eliminar destino de Préstamo
    * @urlParam loan_destiny required ID de destino de Préstamo. Example: 6
    * @authenticated
    * @responseFile responses/loan_destiny/destroy.200.json
    */
    public function destroy(LoanDestiny $loan_destiny)
    {
        if ($loan_destiny->procedure_types()->count()) abort(409, 'Aún existen trámites asociados al destino');
        $loan_destiny->delete();
        return $loan_destiny;
    }
}
