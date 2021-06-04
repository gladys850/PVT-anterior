<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;

/** @group Categoría Policial
* Datos de las categorías policiales disponibles en el sistema
*/
class CategoryController extends Controller
{
    /**
    * Lista de categorías
    * Devuelve el listado de las categorías
    * @authenticated
    * @responseFile responses/category/index.200.json
    */
    public function index()
    {
        return Category::orderBy('name')->get();
    }

    /**
    * Detalle de categoría
    * Devuelve el detalle de una categoría mediante su ID
    * @urlParam category required ID de categoría. Example: 1
    * @authenticated
    * @responseFile responses/category/show.200.json
    */
    public function show(Category $category)
    {
        return $category;
    }
}