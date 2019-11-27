<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Loan;
use App\LoanState;

class LoanController extends Controller
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

    public function switch_states()
    {
        $amortizing_state = LoanState::whereName('Amortizando')->first();
        $defaulted_state = LoanState::whereName('Mora')->first();

        // Switch amortizing loans to defaulted
        $amortizing_loans = Loan::whereLoanStateId($amortizing_state->id)->get();
        foreach ($amortizing_loans as $loan) {
            if ($loan->defaulted) {
                $loan->update('loan_state_id', $defaulted_state->id);
            }
        }

        // Switch defaulted loans to amortizing
        $defaulted_loans = Loan::whereLoanStateId($defaulted_state->id)->get();
        foreach ($defaulted_loans as $loan) {
            if (!$loan->defaulted) {
                $loan->update('loan_state_id', $amortizing_state->id);
            }
        }
    }
}