<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;

/** @group Categorías
* Datos de las categorías policiales disponibles en el sistema
*/
class CategoryController extends Controller
{
    /**
    * Lista de categorías
    * Devuelve el listado de las categorías
    * @response
    * [
    *     {
    *         "id": 1,
    *         "from": 0,
    *         "to": 4,
    *         "name": "0%",
    *         "percentage": "0.00"
    *     }, {}
    * ]
    */
    public function index()
    {
        return Category::get();
    }
}
