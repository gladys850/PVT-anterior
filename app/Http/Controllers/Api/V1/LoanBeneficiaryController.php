<?php

namespace App\Http\Controllers\Api\V1;
use App\LoanBeneficiary;
use App\Http\Requests\LoanBeneficiaryForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Util;

/** @group Personas referencia préstamos
* Datos de las personas de referencia para trámites de préstamos
*/
class LoanBeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Util::search_sort(new LoanBeneficiary(), $request);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoanBeneficiaryForm $request)
    {
        return LoanBeneficiary::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return LoanBeneficiary::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LoanBeneficiaryForm $request, $id)
    {
        $loan_beneficiary = LoanBeneficiary::findOrFail($id);
        $loan_beneficiary->fill($request->all());
        $loan_beneficiary->save();
        return  $loan_beneficiary;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan_beneficiary = LoanBeneficiary::findOrFail($id);
        $loan_beneficiary->delete();
        return $loan_beneficiary;
    }
}
