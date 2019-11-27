<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Loan;
use App\LoanState;
use Illuminate\Support\Facades\Schema;
use App\LoanSubmittedDocument;
use App\ProcedureDocument;


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
           $loans = Loan::query();
            if ($request->has('search')) {
                if ($request->search != 'null' && $request->search != '') {
                    $search = $request->search;
                    $loans = $loans->where(function ($query) use ($search) {
                        foreach (Schema::getColumnListing(Loan::getTableName()) as $column) { 
                            $query = $query->orWhere($column, 'ilike', '%' . $search . '%');
                        }
                    });
                }
            }
            if ($request->has('sortBy')) {
                if (count($request->sortBy) > 0 && count($request->sortDesc) > 0) {
                    foreach ($request->sortBy as $i => $sort) {
                        $loans = $loans->orderBy($sort, filter_var($request->sortDesc[$i], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc');
                    }
                }
            }
            $loans = $loans->paginate($request->per_page ?? 10);
            return $loans;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Loan::create($request->all());
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
    public function update(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $loan->fill($request->all());
        $loan->save();
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