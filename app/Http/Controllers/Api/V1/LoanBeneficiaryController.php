<?php

namespace App\Http\Controllers\Api\V1;
use App\LoanBeneficiary;
use App\Http\Requests\LoanBeneficiaryForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanBeneficiaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $beneficiaries = LoanBeneficiary::query();
        if ($request->has('search')) {
            if ($request->search != 'null' && $request->search != '') {
                $search = $request->search;
                $beneficiaries = $beneficiaries->where(function ($query) use ($search) {
                    foreach (Schema::getColumnListing(LoanBeneficiary::getTableName()) as $column) { 
                        $query = $query->orWhere($column, 'ilike', '%' . $search . '%');
                    }
                });
            }
        }
        if ($request->has('sortBy')) {
            if (count($request->sortBy) > 0 && count($request->sortDesc) > 0) {
                foreach ($request->sortBy as $i => $sort) {
                    $beneficiaries = $beneficiaries->orderBy($sort, filter_var($request->sortDesc[$i], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc');
                }
            }
        }
        $beneficiaries = $beneficiaries->paginate($request->per_page ?? 10);
        return $beneficiaries;
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
