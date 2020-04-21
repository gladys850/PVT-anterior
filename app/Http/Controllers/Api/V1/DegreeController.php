<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Degree;

/** @group Grados
* Datos de los grados policiales disponibles en el sistema
*/
class DegreeController extends Controller
{
    /**
    * Lista de grados
    * Devuelve la lista de los grados policiales
    * @response
    * [
    *     {
    *         "id": 1,
    *         "hierarchy_id": 1,
    *         "code": "00",
    *         "name": "COMANDANTE GRAL",
    *         "shortened": "CMTE. GRAL.",
    *         "correlative": 1
    *     }, {}
    * ]
     */
    public function index(Request $request)
    {
        $query = Degree::query();
        if ($request->has('affiliate')) {
            $query = $query->whereHas('affiliates', function($q) {
                $q->whereId($request->affiliate);
            });
        }
        return $query->get();
    }
}
