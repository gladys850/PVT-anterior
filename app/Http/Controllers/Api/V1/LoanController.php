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

/** @group Préstamos
* Datos de los trámites de préstamos y sus relaciones
*/
class LoanController extends Controller
{
    private function append_data($loan) {
        $loan->balance = $loan->balance;
        $loan->estimated_quota = $loan->estimated_quota;
        $loan->defaulted = $loan->defaulted;
    }
    /**
    * Lista de Préstamos
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: 3000
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *     "current_page": 1,
    *     "data": [
    *         {
    *             "id": 2,
    *             "code": null,
    *             "disbursable_id": 1453,
    *             "disbursable_type": "spouses",
    *             "procedure_modality_id": 32,
    *             "amount_disbursement": 2000,
    *             "disbursement_date": "2020-02-13",
    *             "parent_loan_id": null,
    *             "parent_reason": null,
    *             "request_date": "2020-02-13",
    *             "amount_request": 2000,
    *             "city_id": 4,
    *             "loan_interest_id": 1,
    *             "loan_state_id": 1,
    *             "amount_aproved": null,
    *             "loan_term": 3,
    *             "disbursement_type_id": 1,
    *             "created_at": "2020-02-13 16:32:43",
    *             "updated_at": "2020-02-13 16:32:43",
    *             "balance": 2000,
    *             "estimated_quota": 707.06,
    *             "defaulted": false
    *         }, {} 
    *     ],
    *     "first_page_url": "http://192.168.2.242/api/v1/loan?page=1",
    *     "from": 1,
    *     "last_page": 1,
    *     "last_page_url": "http://192.168.2.242/api/v1/loan?page=1",
    *     "next_page_url": null,
    *     "path": "http://192.168.2.242/api/v1/loan",
    *     "per_page": 10,
    *     "prev_page_url": null,
    *     "to": 3,
    *     "total": 3
    * }
    */
    public function index(Request $request)
    {
        $data = Util::search_sort(new Loan(), $request);
        foreach ($data as $item) {
            $this->append_data($item);
        }
        return $data;
    }

     /**
    * Nuevo préstamo
    * Inserta nuevo préstamo
    * @bodyParam disbursable_id integer required ID de afiliado a quien se hara el desembolso. Example: 1
    * @bodyParam disbursable_type string required (affiliates,spouses). Example: affiliates
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 35
    * @bodyParam amount_request integer required monto solicitado. Example: 3000
    * @bodyParam city_id integer required ID de la ciudad. Example: 2
    * @bodyParam loan_term integer required plazo. Example: 3
    * @bodyParam disbursement_type_id integer required Tipo de desembolso. Example: 1
    * @queryParam lenders required array Lista de IDs de afiliados Titular de préstamo. Example: [1,6]
    * @queryParam guarantors required array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam amount_disbursement integer Monto desembolsado. Example: 3000
    * @bodyParam disbursement_date date Fecha de desembolso. Example: 2020-02-01
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (refinanciado,reprogramado)  . Example: refinanciado
    * @bodyParam amount_aproved integer Monto Provado. Example: 3000
    * @authenticated
    * @response
    * {
    *    "disbursable_id": "1",
    *    "disbursable_type": "affiliates",
    *    "procedure_modality_id": 35,
    *    "loan_interest_id": 4,
    *    "amount_request": 3000,
    *    "city_id": 2,
    *    "loan_term": 3,
    *    "disbursement_type_id": 1,
    *    "code": null,
    *    "amount_disbursement": "3000",
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": "1",
    *    "parent_reason": null,
    *    "amount_aproved": "3000",
    *    "loan_state_id": 1,
    *    "request_date": "2020-02-17T13:38:18.562649Z",
    *    "updated_at": "2020-02-17 09:38:18",
    *    "created_at": "2020-02-17 09:38:18",
    *    "id": 5,
    *    "modality": {
    *        "id": 35,
    *        "procedure_type_id": 10,
    *        "name": "Corto plazo con disponibilidad de letra \"A\"",
    *        "shortened": "PCP-DLA",
    *        "is_valid": true
    *    }
    * }
    */
    public function store(LoanForm $request)
    {
        if($request->lenders){
            $lenders_guarantors = array_merge($request->lenders,$request->guarantors);
            foreach($lenders_guarantors as $affiliate) {
                if(!Affiliate::whereId($affiliate)->exists()) abort (404); 
            }
            $loan = new Loan($request->all());
            $affiliate = Affiliate::findOrFail($request->disbursable_id);
            if($affiliate->dead){
                $spouse = $affiliate->spouse; 
                if($spouse){
                    $loan->disbursable_id = $spouse->id;
                    $loan->disbursable_type = 'spouses' ; 
                }
            }
            $loan->code = Loan::get_code();
            $loan->save();
            $request->lenders = collect($request->lenders)->unique();
            $request->guarantors = collect($request->guarantors)->unique();
            $request->guarantors = $request->guarantors->diff($request->lenders);
            $percentage = Loan::get_percentage($request->lenders);
            foreach ($request->lenders as $affiliate) {
                $affiliates[$affiliate] = [
                    'payment_percentage' =>$percentage,
                    'guarantor' => false
                ];
            }
            if($request->guarantors){
                $percentage = Loan::get_percentage($request->guarantors);
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
    * Detalle de Préstamo
    * Devuelve el detalle de un préstamo mediante su ID
    * @urlParam loan required ID de préstamo. Example: 4
    * @response
    * {
    *    "id": 4,
    *    "code": null,
    *    "disbursable_id": 1,
    *    "disbursable_type": "affiliates",
    *    "procedure_modality_id": 35,
    *    "amount_disbursement": 3000,
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": null,
    *    "parent_reason": null,
    *    "request_date": "2020-02-17",
    *    "amount_request": 3000,
    *    "city_id": 2,
    *    "loan_interest_id": 4,
    *    "loan_state_id": 1,
    *    "amount_aproved": 3000,
    *    "loan_term": 3,
    *    "disbursement_type_id": 1,
    *    "created_at": "2020-02-17 14:52:40",
    *    "updated_at": "2020-02-17 14:52:40",
    *    "balance": 3000,
    *    "estimated_quota": 1033.52,
    *    "defaulted": false,
    *    "lenders": [
    *        {
    *            "id": 6,
    *            "user_id": 11,
    *            "affiliate_state_id": 4,
    *            "city_identity_card_id": 4,
    *            "city_birth_id": 2,
    *            "degree_id": 7,
    *            "unit_id": 1,
    *            "category_id": 8,
    *            "pension_entity_id": 2,
    *            "identity_card": "2022760",
    *            "registration": "500718RRA",
    *            "type": "Comando",
    *            "last_name": "RODRIGUEZ",
    *            "mothers_last_name": "ROLDAN",
    *            "first_name": "ALBERTO",
    *            "second_name": null,
    *            "surname_husband": null,
    *            "gender": "M",
    *            "civil_status": "C",
    *            "birth_date": "1950-07-18",
    *            "date_entry": "1972-01-01",
    *            "date_death": "2015-08-08",
    *            "reason_death": "TRAUMATISMO CRANEO ENCEFÁLICO GRAVE",
    *            "date_derelict": null,
    *            "reason_derelict": null,
    *            "change_date": "2016-07-01",
    *            "phone_number": "(6) 423-792",
    *            "cell_phone_number": "(603)-18901",
    *            "afp": true,
    *            "nua": 17346472,
    *            "item": 27468,
    *            "created_at": "2017-06-01 10:42:18",
    *            "updated_at": "2018-09-05 07:59:09",
    *            "deleted_at": null,
    *            "service_years": null,
    *            "service_months": null,
    *            "death_certificate_number": "59244",
    *            "due_date": null,
    *            "is_duedate_undefined": true,
    *            "affiliate_registration_number": null,
    *            "file_code": null,
    *            "pivot": {
    *                "loan_id": 4,
    *                "affiliate_id": 6,
    *                "payment_percentage": "50"
    *            }
    *        },{}
    *   ],
    *   "guarantors": []
    * }
    */
    public function show($id)
    {
        $item = Loan::findOrFail($id);
        $item->lenders;
        $item->guarantors;
        $this->append_data($item);
        return $item;
    }

    /**
    * Actualizar préstamo
    * Actualizar datos principales de préstamo
    * @urlParam id required ID de préstamo. Example: 1
    * @bodyParam disbursable_id integer required ID de afiliado a quien se hara el desembolso. Example: 1
    * @bodyParam disbursable_type string required (affiliates,spouses). Example: affiliates
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 35
    * @bodyParam amount_request integer required monto solicitado. Example: 5000
    * @bodyParam city_id integer required ID de la ciudad. Example: 2
    * @bodyParam loan_term integer required plazo. Example: 3
    * @bodyParam disbursement_type_id integer required Tipo de desembolso. Example: 1
    * @queryParam lenders required array Lista de IDs de afiliados Titular de préstamo. Example: [1,6]
    * @queryParam guarantors required array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam amount_disbursement integer Monto desembolsado. Example: 3000
    * @bodyParam disbursement_date date Fecha de desembolso. Example: 2020-02-01
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (refinanciado,reprogramado)  . Example: refinanciado
    * @bodyParam amount_aproved integer Monto Aprobado. Example: 3000
    * @authenticated
    * @response
    * {
    *    "id": 1,
    *    "code": null,
    *    "disbursable_id": "1",
    *    "disbursable_type": "affiliates",
    *    "procedure_modality_id": 35,
    *    "amount_disbursement": "3000",
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": null,
    *    "parent_reason": null,
    *    "request_date": "2020-02-17",
    *    "amount_request": 5000,
    *    "city_id": 2,
    *    "loan_interest_id": 4,
    *    "loan_state_id": 1,
    *    "amount_aproved": "3000",
    *    "loan_term": 3,
    *    "disbursement_type_id": 1,
    *    "created_at": "2020-02-17 10:37:48",
    *    "updated_at": "2020-02-17 10:39:41",
    *    "modality": {
    *        "id": 35,
    *        "procedure_type_id": 10,
    *        "name": "Corto plazo con disponibilidad de letra \"A\"",
    *        "shortened": "PCP-DLA",
    *        "is_valid": true
    *    }
    * }
    */
    public function update(LoanForm $request, $id)
    {
        if($request->lenders){
            $lenders_guarantors = array_merge($request->lenders,$request->guarantors);
            foreach($lenders_guarantors as $affiliate) {
                if(!Affiliate::whereId($affiliate)->exists()) abort (404); 
            }
            $loan = Loan::findOrFail($id);
            $loan->fill($request->all());
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
            $percentage = Loan::get_percentage($request->lenders);
            foreach ($request->lenders as $affiliate) {
                $affiliates[$affiliate] = [
                    'payment_percentage' =>$percentage,
                    'guarantor' => false
                ];
            }
            if($request->guarantors){
                $percentage = Loan::get_percentage($request->guarantors);
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
    * Eliminar préstamo
    * @urlParam id required ID de préstamo. Example: 1
    * @authenticated
    * @response
    * {
    *    "id": 1,
    *    "code": null,
    *    "disbursable_id": 1,
    *    "disbursable_type": "affiliates",
    *    "procedure_modality_id": 35,
    *    "amount_disbursement": 3000,
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": null,
    *    "parent_reason": null,
    *    "request_date": "2020-02-17",
    *    "amount_request": 5000,
    *    "city_id": 2,
    *    "loan_interest_id": 4,
    *    "loan_state_id": 1,
    *    "amount_aproved": 3000,
    *    "loan_term": 3,
    *    "disbursement_type_id": 1,
    *    "created_at": "2020-02-17 10:37:48",
    *    "updated_at": "2020-02-17 10:39:41"
    * }
    */
    public function destroy($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->loan_affiliates()->sync([]);
        $loan->delete();
        return $loan;
    }

    /**
    * Registro de documentos presentados
    * Registra todos los documentos entregados por parte del prestatario
    * @urlParam id required ID de préstamo. Example: 2
    * @queryParam documents required array Lista de IDs de Documentos solicitados. Example: [306,305]
    * @response
    * {
    *    "attached": [
    *        306,
    *        305
    *    ],
    *    "detached": [],
    *    "updated": []
    * }
    */
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

    /**
    * Desembolso Afiliado
    * Devuelve los datos del o la cónyugue en caso de que hubiera fallecido a quien se hace el desembolso del préstamo
    * @urlParam id required ID de préstamo. Example: 2
    * @response
    * {
    *     "id": 1,
    *    "user_id": 1,
    *    "affiliate_state_id": 5,
    *    "city_identity_card_id": 2,
    *    "city_birth_id": 2,
    *    "degree_id": 7,
    *    "unit_id": 1,
    *    "category_id": 8,
    *    "pension_entity_id": 2,
    *    "identity_card": "1020566",
    *    "registration": "440808ACG",
    *    "type": "Comando",
    *    "last_name": "ALVAREZ",
    *    "mothers_last_name": "CURCUY",
    *    "first_name": "GARY",
    *    "second_name": null,
    *    "surname_husband": null,
    *    "gender": "M",
    *    "civil_status": "C",
    *    "birth_date": "1944-08-08",
    *    "date_entry": "2010-07-19",
    *    "date_death": null,
    *    "reason_death": "",
    *    "date_derelict": "2017-04-01",
    *    "reason_derelict": null,
    *    "change_date": "2016-07-01",
    *    "phone_number": "(6) 446-978",
    *    "cell_phone_number": "",
    *    "afp": true,
    *    "nua": 1301101,
    *    "item": 27446,
    *    "created_at": "2017-06-01 10:42:18",
    *    "updated_at": "2019-06-12 17:08:45",
    *    "deleted_at": null,
    *    "service_years": 41,
    *    "service_months": 10,
    *    "death_certificate_number": "",
    *    "due_date": null,
    *    "is_duedate_undefined": true,
    *    "affiliate_registration_number": null,
    *    "file_code": null
    * }
    */
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

    /**
    * Impresión de los requisitos
    * Devuelve un pdf de los requisitos acorde a una modalidad
    * @queryParam lenders required array Lista de IDs de afiliados Titular de préstamo. Example: [1,6]
    * @bodyParam procedure_modality_id integer required ID de la modalidad del préstamo. Example: 35
    * @bodyParam disbursable_id integer required ID de afiliado a quien se hara el desembolso. Example: 1
    * @bodyParam city_id integer required ID de la ciudad. Example: 2
    * @bodyParam amount_request integer monto solicitado. Example: 5000
    * @bodyParam loan_term integer plazo. Example: 3
    * @bodyParam parent_loan_id integer ID de préstamo padre. Example: 1
    * @authenticated
    */
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
