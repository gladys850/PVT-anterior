<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Spouse;
use App\Http\Requests\SpouseForm;
use Illuminate\Http\Request;
class SpouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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
    public function store(SpouseForm $request)
    {        
        return Spouse::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Spouse  $spouse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $spouse = Spouse::findOrFail($id);
        $this->append_data($spouse);
        return $spouse;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Spouse  $spouse
     * @return \Illuminate\Http\Response
     */
    public function update(SpouseForm $request, $id)
    {
        $spouse = Spouse::findOrFail($id);
        $spouse->fill($request->all());
        $spouse->save();
        return $spouse;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Spouse  $spouse
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $spouse = Spouse::findOrFail($id);
        $spouse->delete();
        return $spouse;
    }
}
