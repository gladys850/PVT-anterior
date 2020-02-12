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
    * @response
    * [
    *     {
    *         "id": 1,
    *         "name": "BENI",
    *         "first_shortened": "BE",
    *         "second_shortened": "BEN",
    *         "third_shortened": "BNI",
    *         "to_bank": "BE",
    *         "latitude": -14.834909060283,
    *         "longitude": -64.904201030731
    *     }, {}
    * ]
     */
    public function index()
    {
        return City::orderBy('name')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
