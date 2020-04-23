<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\City;

/** @group Ciudades
* Datos de las ciudades disponibles en el sistema
*/
class CityController extends Controller
{
    /**
    * Lista de ciudades
    * Devuelve el listado de las ciudades
    * @authenticated
    * @responseFile responses/city/index.200.json
    */
    public function index()
    {
        return City::orderBy('name')->get();
    }

    /**
    * Detalle de ciudad
    * Devuelve el detalle de una ciudad mediante su ID
    * @urlParam city required ID de ciudad. Example: 1
    * @authenticated
    * @responseFile responses/city/show.200.json
    */
    public function show(City $city)
    {
        return $city;
    }
}
