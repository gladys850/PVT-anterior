<?php

namespace App\Http\Controllers\Api\V1;

use Util;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Affiliate;
use App\City;
use App\Loan;
use App\LoanState;
use Illuminate\Support\Facades\Schema;
use App\ProcedureDocument;
use App\ProcedureModality;
use App\Http\Requests\LoanForm;
use Carbon;

class LoanController extends Controller
{
    private function append_data($loan) {
        $loan->balance = $loan->balance;
        $loan->estimated_quota = $loan->estimated_quota;
        $loan->defaulted = $loan->defaulted;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $loan = new Loan($request->all());
        foreach ($loan as $item) {
            $this->append_data($item);
        }
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
        return new Loan();
        if($request->lenders){
            $lenders_guarantors = array_merge($request->lenders,$request->guarantors);
            foreach($lenders_guarantors as $affiliate) {
                if(!Affiliate::whereId($affiliate)->exists()) abort (404); 
            }
            $loan = new Loan($request->all());
            return $loan;
            $affiliate = Affiliate::findOrFail($request->disbursable_id);
            if($affiliate->dead){
                $spouse = $affiliate->spouse; 
                if($spouse){
                    $loan->disbursable_id = $spouse->id;
                    $loan->disbursable_type = 'spouses' ; 
                }
            }
            $loan->save();


            
            $request->lenders = collect($request->lenders)->unique();
            $request->guarantors = collect($request->guarantors)->unique();
            $request->guarantors = $request->guarantors->diff($request->lenders);
            $res = new Loan();
            $percentage = $res->percentage($request->lenders);
            foreach ($request->lenders as $affiliate) {
                $affiliates[$affiliate] = [
                    'payment_percentage' =>$percentage,
                    'guarantor' => false
                ];
            }
            if($request->guarantors){
                $percentage = $res->percentage($request->guarantors);
                foreach ($request->guarantors as $affiliate) {
                    $affiliates[$affiliate] = [
                        'payment_percentage' =>$percentage,
                        'guarantor' => true
                    ];
                }
            }
            $loan->loan_affiliates()->sync($affiliates);
            return $loan;
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Loan::findOrFail($id);
        $this->append_data($item);
        return $item;
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
        if($request->lenders){
            $loan = Loan::findOrFail($id);
            $loan->fill($request->all());
            $loan->save();
            $lenders_guarantors = array_merge($request->lenders,$request->guarantors);
            foreach($lenders_guarantors as $affiliate) {
                if(!Affiliate::whereId($affiliate)->exists()) abort (404); 
            }
            $request->lenders = collect($request->lenders)->unique();
            $request->guarantors = collect($request->guarantors)->unique();
            $request->guarantors = $request->guarantors->diff($request->lenders);
            $res = new Loan();
            $percentage = $res->percentage($request->lenders);
            foreach ($request->lenders as $affiliate) {
                $affiliates[$affiliate] = [
                    'payment_percentage' =>$percentage,
                    'guarantor' => false
                ];
            }
            if($request->guarantors){
                $percentage = $res->percentage($request->guarantors);
                foreach ($request->guarantors as $affiliate) {
                    $affiliates[$affiliate] = [
                        'payment_percentage' =>$percentage,
                        'guarantor' => true
                    ];
                }
            }
            $loan->loan_affiliates()->sync($affiliates);
            return $loan;
        }
        abort(404);
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
        $loan->loan_affiliates()->sync([]);
        $loan->delete();
        return $loan;
    }

    public function submit_documents(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $date = Carbon::now()->toISOString();
        $documents = [];
        foreach ($request->documents as $document_id) {
            $document_id = intval($document_id);
            ProcedureDocument::findOrFail($document_id);
            if ($loan->submitted_documents()->whereId($document_id)->doesntExist()) {
                $documents[$document_id] = [
                    'reception_date' => $date
                ];
            }
        }
        return $loan->submitted_documents()->syncWithoutDetaching($documents);
    }

    public function get_disbursable($id){
        $disbursable = Loan::findOrFail($id);
        return $disbursable->disbursable;
    }

    private function verify_spouse_disbursable($disbursable_id)
    {
        $affiliate = Affiliate::findOrFail($disbursable_id);
        if ($affiliate->dead) {
            $spouse = $affiliate->spouse;
            if ($spouse) return (object)[
                'disbursable_type' => 'spouses',
                'disbursable_id' => $spouse->id,
                'disbursable' => $spouse
            ];
        }
        return (object)[
            'disbursable_type' => 'affiliates',
            'disbursable_id' => $affiliate->id,
            'disbursable' => $affiliate
        ];
    }

    public function print_requirements(Request $request)
    {
        $parent_loan = $request->has('parent_loan_id') ? Loan::find($request->parent_loan_id) : null;
        $lenders = [];
        foreach ($request->lenders as $lender) {
            array_push($lenders, $this->verify_spouse_disbursable($lender)->disbursable);
        }
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $date = Carbon::now();
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Tipo', $procedure_modality->procedure_type->second_name],
                    ['Modalidad', $procedure_modality->shortened],
                    ['Usuario', auth()->user()->username ?? 'prueba']
                ]
            ],
            'title' => 'REQUISITOS PARA SOLICITUD DE ' . ($parent_loan ? 'REFINANCIAMIENTO' : 'PRÉSTAMO'),
            'lenders' => $lenders,
            'procedure_modality' => $procedure_modality,
            'city' => City::findOrFail($request->city_id),
            'disbursable' => $this->verify_spouse_disbursable($request->disbursable_id),
            'parent_loan' => $parent_loan,
            'amount_request' => $request->amount_request,
            'loan_term' => $request->loan_term
        ];
        $file_name = implode('_', ['solicitud', 'prestamo']) . '.pdf';
        $footerHtml = view()->make('partials.footer')->with(array('paginator' => true, 'print_date' => true, 'date' => Carbon::now()->ISOFormat('L H:m')))->render();
        $options = [
            'orientation' => 'portrait',
            'page-width' => '216',
            'page-height' => '279',
            'margin-top' => '8',
            'margin-bottom' => '16',
            'margin-left' => '5',
            'margin-right' => '7',
            'encoding' => 'UTF-8',
            'footer-html' => $footerHtml,
            'user-style-sheet' => public_path('css/report-print.min.css')
        ];
        $pdf = \PDF::loadView('procedure_modality.requirements', $data);
        $pdf->setOptions($options);
        return $pdf->stream($file_name);
    }
}
