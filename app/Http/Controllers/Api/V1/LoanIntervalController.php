<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LoanInterval;

/** @group Préstamos
*/
class LoanIntervalController extends Controller
{
    /**
    * Intérvalos
    * Devuelve el listado de los intérvalos de montos y plazos por tipo de trámite de préstamo
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 4,
    *         "maximum_amount": 700000,
    *         "minimum_amount": 25001,
    *         "maximum_term": 240,
    *         "minimum_term": 25,
    *         "procedure_type_id": 12
    *     }, {}
    * ]
    */
    public function index()
    {
        return LoanInterval::orderByDesc('maximum_amount')->get();
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
