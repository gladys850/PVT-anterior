<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calculator;


class CalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
    public function get_calculator(Request $request){
        //$ticket = [2000];$bonuses = [24,24,24,0]; $modality_id=32;$months_term=null;$amount_request=1000;$quota_payment=null;$affiliate_id=2;
        $calculator = new Calculator();
        $calculation = $calculator->calculator($request->ticket,$request->bonuses,$request->modality_id,$request->months_term,$request->amount_request,$request->affiliate_id);//$request->ticket
        return $calculation;
        /*
        $calculation = $calculator->calculator($ticket,$bonuses,$modality_id,$months_term,$amount_request,$affiliate_id);//$request->ticket
        $calculator = new Calculator();
        $calculation = $calculator->liquid_qualification();//$request->ticket
        return $calculation;*/
    }
}
