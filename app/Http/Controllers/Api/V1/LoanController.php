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
use Carbon;



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
        return Loan::findOrFail($id);
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

    public function create_request($loan_id){
        $loan=new Loan();
        $amount_disbur=$loan->find($loan_id)->amount_disbursement;
        $dat= $loan->find($loan_id);
        $affiliate=$dat->loan_affiliates;
        $data = [
            'dat' => $dat,
            'affiliate' => $affiliate,
            'amount_disbur' => $amount_disbur
        ];
        $year = Carbon::now()->format('Y');
        $file_name = "Solicitud de ".$year. ".pdf";
        $options = [
            'orientation' => 'portrait',
            'page-width' => '216',
            'page-height' => '279',
            'margin-top' => '4',
            'margin-bottom' => '4',
            'margin-left' => '5',
            'margin-right' => '5',
            'encoding' => 'UTF-8',
            'user-style-sheet' => public_path('css/report-print.min.css')
          ];
          $pdf = PDF::loadView('request',$data);
          $pdf->setOptions($options);
          return $pdf->stream($file_name);
        }
}