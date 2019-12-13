<?php

namespace App\Http\Controllers\Api\V1;

use Util;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Loan;
use App\LoanState;
use Illuminate\Support\Facades\Schema;
use App\LoanSubmittedDocument;
use App\ProcedureDocument;
use App\Http\Requests\LoanForm;



class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return Loan::get();
        $data = Util::search_sort(new Loan(), $request);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoanForm $request)
    {
        $loan = Loan::create($request->all());
        foreach ($request->affiliates as $affiliate) {
            $loan->loan_affiliates()->attach($affiliate);//$loan->loan_affiliates()->attach(25, ['payment_porcentage' =>23]);
        }
        return $loan;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $loan = Loan::findOrFail($id);
        $this->append_data($loan);
        return $loan;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LoanForm $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->fill($request->all());
        $loan->save();
        if ($request->affiliates) {
            $loan->loan_affiliates()->detach();
            foreach ($request->affiliates as $affiliate) {
              $loan->loan_affiliates()->attach($affiliate);
            }
        }
        return  $loan;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->delete();
        return $loan;
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
    //obtener lista de requisitos teniendo registrado un prestamo con una modalidad registrada
    public function list_requirements($loan_id){
       $loan=Loan::find($loan_id) ; 
       return $loan->modality->procedure_documents;// listar requisitos de acuerdo a una modalidad
    }
    // obtener doc. entregados de un prestamo en especifico
    public function submitted_documents($loan_id){
        $sub= LoanSubmittedDocument::whereLoan_id($loan_id)->get();
        $name=[]; $i=1;
        foreach($sub as $res){ 
            $name[$i]=ProcedureDocument::find($res->procedure_document_id); $i++; 
        }
        return $name;
    }
}