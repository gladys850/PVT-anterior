<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Degree;

/** @group Grado Policial
* Datos de los grados policiales disponibles en el sistema
*/
class DegreeController extends Controller
{
    /**
    * Lista de grados
    * Devuelve la lista de los grados policiales
    * @authenticated
    * @responseFile responses/degree/index.200.json
    */
    public function index()
    {
        return Degree::orderBy('name')->get();
    }

    /**
    * Detalle de grado
    * Devuelve el detalle de un grado policial mediante su ID
    * @urlParam degree required ID de grado policial. Example: 1
    * @authenticated
    * @responseFile responses/degree/show.200.json
    */
    public function show(Degree $degree)
    {
        return $degree;
    }
}
