<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Storage;
use Util;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Affiliate;
use App\City;
use App\User;
use App\Loan;
use App\Tag;
use App\LoanState;
use App\RecordType;
use Illuminate\Support\Facades\Schema;
use App\ProcedureDocument;
use App\ProcedureModality;
use App\PaymentType;
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
    *             "disbursement_date": "2020-02-13",
    *             "parent_loan_id": null,
    *             "parent_reason": null,
    *             "request_date": "2020-02-13",
    *             "amount_requested": 2000,
    *             "city_id": 4,
    *             "loan_interest_id": 1,
    *             "loan_state_id": 1,
    *             "amount_approved": null,
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
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 32
    * @bodyParam amount_requested integer required monto solicitado. Example: 2000
    * @bodyParam city_id integer required ID de la ciudad. Example: 3
    * @bodyParam loan_term integer required plazo. Example: 2
    * @bodyParam disbursement_type_id integer required Tipo de desembolso. Example: 1
    * @bodyParam lenders array required Lista de IDs de afiliados Titular de préstamo. Example: [5146]
    * @bodyParam guarantors array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (REFINANCIAMIENTO, REPROGRAMACIÓN) Tipo de trámite hijo. Example: REFINANCIAMIENTO
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam loan_destination_id integer required ID destino de Préstamo. Example: 1
    * @bodyParam documents array required Lista de IDs de Documentos solicitados. Example: [306,305]
    * @bodyParam notes array Lista de notas aclaratorias. Example: [Informe de baja policial, Carta de solicitud]
    * @authenticated
    * @response
    * {
    *   "procedure_modality_id": 32,
    *   "loan_interest_id": 1,
    *   "amount_requested": 2000,
    *   "city_id": 3,
    *   "loan_term": 2,
    *   "disbursement_type_id": 1,
    *   "loan_destination_id": 1,
    *   "account_number": 586621345,
    *   "request_date": "2020-03-05T20:27:23.900575Z",
    *   "disbursable_type": "affiliates",
    *   "disbursable_id": 5146,
    *   "amount_approved": 2000,
    *   "loan_state_id": 1,
    *   "code": "PTMO000017-2020",
    *   "disbursement_date": "2020-02-01",
    *   "updated_at": "2020-03-05 16:27:23",
    *   "created_at": "2020-03-05 16:27:23",
    *   "parent_loan_id": 1,
    *   "parent_reason": "REFINANCIAMIENTO",
    *   "id": 17,
    *   "modality": {
    *       "id": 32,
    *       "procedure_type_id": 9,
    *       "name": "Anticipo sector activo",
    *       "shortened": "ANT-SA",
    *       "is_valid": true
    *   },
    *   "attachment": {
    *       "content": "zMTcgM....",
    *       "type": "pdf",
    *       "file_name": "solicitud_prestamo_17.pdf"
    *   }
    * }
    */
    public function store(LoanForm $request)
    {
        // Guardar préstamo
        $saved = $this->save_loan($request);
        // Relacionar afiliados y garantes
        $loan = $saved->loan;
        $request = $saved->request;
        $request->lenders = collect($request->lenders);
        $request->guarantors = collect($request->guarantors);
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
        // Relacionar documentos requeridos y opcionales
        $date = Carbon::now()->toISOString();
        $documents = [];
        foreach ($request->documents as $document_id) {
            if ($loan->submitted_documents()->whereId($document_id)->doesntExist()) {
                $documents[$document_id] = [
                    'reception_date' => $date
                ];
            }
        }
        $loan->submitted_documents()->syncWithoutDetaching($documents);
        // Relacionar notas
        foreach ($request->notes as $message) {
            $loan->notes()->create([
                'message' => $message,
                'date' => Carbon::now()
            ]);
        }
        // Generar PDFs
        $file_name = implode('_', ['solicitud', 'prestamo', $loan->id]) . '.pdf';
        $loan->attachment = Util::pdf_to_base64([
            $this->print_form(new Request([]), $loan->id, false),
            $this->print_documents(new Request([]), $loan->id, false),
            $this->print_contract(new Request([]), $loan->id, false)
        ], $file_name, 'letter', $request->copies ?? 1);
        return $loan;
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
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": null,
    *    "parent_reason": null,
    *    "request_date": "2020-02-17",
    *    "amount_requested": 3000,
    *    "city_id": 2,
    *    "loan_interest_id": 4,
    *    "loan_state_id": 1,
    *    "amount_approved": 3000,
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
    * @urlParam id required ID de préstamo. Example: 17
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 32
    * @bodyParam amount_requested integer required monto solicitado. Example: 2000
    * @bodyParam city_id integer required ID de la ciudad. Example: 4
    * @bodyParam loan_term integer required plazo. Example: 2
    * @bodyParam disbursement_type_id integer required Tipo de desembolso. Example: 1
    * @bodyParam lenders array required Lista de IDs de afiliados Titular de préstamo. Example: [5146]
    * @bodyParam guarantors array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam disbursement_date date Fecha de desembolso. Example: 2020-02-01
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (REFINANCIAMIENTO, REPROGRAMACIÓN) Tipo de trámite hijo. Example: REFINANCIAMIENTO
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam loan_destination_id integer required ID destino de Préstamo. Example: 1
    * @authenticated
    * @response
    * {
    *     "id": 17,
    *     "code": "PTMO000017-2020",
    *     "disbursable_id": 5146,
    *     "disbursable_type": "affiliates",
    *     "procedure_modality_id": 32,
    *     "disbursement_date": "2020-02-01",
    *     "parent_loan_id": 1,
    *     "parent_reason": "REFINANCIAMIENTO",
    *     "request_date": "2020-03-05",
    *     "amount_requested": 2000,
    *     "city_id": 4,
    *     "loan_interest_id": 1,
    *     "loan_state_id": 1,
    *     "amount_approved": 2000,
    *     "loan_term": 2,
    *     "disbursement_type_id": 1,
    *     "loan_destination_id": 1,
    *     "account_number": 586621345,
    *     "created_at": "2020-03-05 16:27:23",
    *     "updated_at": "2020-03-05 16:34:04",
    *     "modality": {
    *         "id": 32,
    *         "procedure_type_id": 9,
    *         "name": "Anticipo sector activo",
    *         "shortened": "ANT-SA",
    *         "is_valid": true
    *     }
    * }
    */
    public function update(LoanForm $request, $id)
    {
        $saved = $this->save_loan($request, $id);
        $loan = $saved->loan;
        $request = $saved->request;
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
    *    "disbursement_date": "2020-02-01",
    *    "parent_loan_id": null,
    *    "parent_reason": null,
    *    "request_date": "2020-02-17",
    *    "amount_requested": 5000,
    *    "city_id": 2,
    *    "loan_interest_id": 4,
    *    "loan_state_id": 1,
    *    "amount_approved": 3000,
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

    private function save_loan(Request $request, $id = null)
    {
        if (!$request->has('guarantors')) $request->guarantors = [];
        $request->lenders = array_unique($request->lenders);
        $request->guarantors = array_unique($request->guarantors);
        if (!$request->has('disbursable_id')) {
            $disbursable_id = $request->lenders[0];
        } else {
            if (!in_array($request->disbursable_id, $request->lenders)) abort(404);
            $disbursable_id = $request->disbursable_id;
        }
        $disbursable = Affiliate::findOrFail($disbursable_id);
        if ($id) {
            $loan = Loan::findOrFail($id);
            $loan->fill(array_merge($request->all(), (array)self::verify_spouse_disbursable($disbursable)));
        } else {
            $loan = new Loan(array_merge($request->all(), (array)self::verify_spouse_disbursable($disbursable), ['amount_approved' => $request->amount_requested]));
        }
        $loan->save();
        return (object)[
            'request' => $request,
            'loan' => $loan
        ];
    }

    /**
    * Actualización de documentos presentados
    * Actualiza los datos para cada documento presentado
    * @urlParam loan_id required ID de préstamo. Example: 8
    * @urlParam document_id required ID de préstamo. Example: 40
    * @bodyParam is_valid boolean required Validez del documento. Example: true
    * @bodyParam comment string Comentario para añadir a la presentación. Example: Documento actualizado a la gestión actual
    * @response
    * [
    *     {
    *         "id": 40,
    *         "name": "Cédula de Identidad del (la) titular en copia simple.",
    *         "created_at": "2019-04-02 21:25:32",
    *         "updated_at": null,
    *         "expire_date": null,
    *         "pivot": {
    *             "loan_id": 8,
    *             "procedure_document_id": 40,
    *             "reception_date": "2020-03-06",
    *             "comment": "Documento actualizado a la gestión actual",
    *             "is_valid": true
    *         }
    *     }, {}
    * ]
    */
    public function update_document(Request $request, $loan_id, $document_id)
    {
        $request->validate([
            'is_valid' => 'required|boolean',
            'comment' => 'string|nullable|min:1'
        ]);
        $loan = Loan::findOrFail($loan_id);
        $loan->submitted_documents()->updateExistingPivot($document_id, $request->all());
        return $loan->submitted_documents;
    }

    /**
    * Lista de documentos presentados
    * Obtiene la lista de los documentos presentados para el trámite
    * @urlParam id required ID de préstamo. Example: 8
    * @response
    * [
    *     {
    *         "id": 40,
    *         "name": "Cédula de Identidad del (la) titular en copia simple.",
    *         "created_at": "2019-04-02 21:25:32",
    *         "updated_at": null,
    *         "expire_date": null,
    *         "pivot": {
    *             "loan_id": 8,
    *             "procedure_document_id": 40,
    *             "reception_date": "2020-03-06",
    *             "comment": "Documento actualizado a la gestión actual",
    *             "is_valid": true
    *         }
    *     }, {}
    * ]
    */
    public function get_documents($id)
    {
        $loan = Loan::findOrFail($id);
        return $loan->submitted_documents;
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
        $loan = Loan::findOrFail($id);
        return $loan->disbursable;
    }

    public static function verify_spouse_disbursable(Affiliate $affiliate)
    {
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
    * Impresión de los documentos presentados
    * Devuelve un pdf de los documentos presentados para un préstamo registrado
    * @urlParam id required ID del préstamo. Example: 6
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    */
    public function print_documents(Request $request, $id, $standalone = true)
    {
        $loan = Loan::findOrFail($id);
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            array_push($lenders, self::verify_spouse_disbursable($lender)->disbursable);
        }
        $date = Carbon::now();
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Tipo', $loan->modality->procedure_type->second_name],
                    ['Modalidad', $loan->modality->shortened],
                    ['Usuario', auth()->user()->username ?? 'prueba']
                ]
            ],
            'title' => 'DOCUMENTOS PRESENTADOS PARA SOLICITUD DE ' . ($loan->parent_loan ? $loan->parent_reason : 'PRÉSTAMO'),
            'loan' => $loan,
            'lenders' => $lenders
        ];
        $file_name = implode('_', ['solicitud', 'prestamo']) . '.pdf';
        $view = view()->make('loan.forms.submitted_documents')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'letter', $request->copies ?? 1);
        return $view;
    }

    public function switch_states()
    {
        $user = User::whereUsername('admin')->first();
        $amortizing_tag = Tag::whereSlug('amortizando')->first();
        $defaulted_tag = Tag::whereSlug('mora')->first();
        $defaulted_loans = 0;
        $amortizing_loans = 0;

        // Switch amortizing loans to defaulted
        $loans = Loan::whereHas('state', function($query) {
            $query->whereName('Desembolsado');
        })->whereHas('tags', function($q) {
            $q->whereSlug('amortizando');
        })->get();
        foreach ($loans as $loan) {
            if ($loan->defaulted) {
                $loan->tags()->detach($amortizing_tag);
                $loan->tags()->attach([$defaulted_tag->id => [
                    'user_id' => $user->id,
                    'date' => Carbon::now()
                ]]);
                $defaulted_loans++;
                foreach ($loan->lenders as $lender) {
                    $lender->records()->create([
                        'user_id' => $user->id,
                        'record_type_id' => RecordType::whereName('etiquetas')->first()->id,
                        'action' => 'etiquetó en mora'
                    ]);
                }
            }
        }

        // Switch defaulted loans to amortizing
        $loans = Loan::whereHas('state', function($query) {
            $query->whereName('Desembolsado');
        })->whereHas('tags', function($q) {
            $q->whereSlug('mora');
        })->get();
        foreach ($loans as $loan) {
            if (!$loan->defaulted) {
                $loan->tags()->detach($defaulted_tag);
                $loan->tags()->attach([$amortizing_tag->id => [
                    'user_id' => $user->id,
                    'date' => Carbon::now()
                ]]);
                $amortizing_loans++;
            }
        }

        return response()->json([
            'defaulted' => $defaulted_loans,
            'amortizing' => $amortizing_loans
        ]);
    }

    /**
    * Impresión de Contrato
    * Devuelve un pdf del contrato acorde a un ID de préstamo
    * @urlParam id required ID del préstamo. Example: 6
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @response
    */
    public function print_contract(Request $request, $id, $standalone = true)
    {
        $loan = Loan::findOrFail($id);
        $procedure_modality = $loan->modality;
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            $lenders[] = self::verify_spouse_disbursable($lender);
        }
        $employees = [
            ['position' => 'Director General Ejecutivo'],
            ['position' => 'Director de Asuntos Administrativos']
        ];
        foreach ($employees as $key => $employee) {
            $req = collect(json_decode(file_get_contents(env("RRHH_URL") . '/position?name=' . $employee['position']), true));
            if ($req->count() == 1) {
                $position = $req->first();
            } else {
                abort(404);
            }
            $req = collect(json_decode(file_get_contents(implode('/', [env("RRHH_URL"), 'position', $position['id'], 'employee'])), true));
            $employees[$key]['name'] = Util::trim_spaces(implode(' ', [$req['first_name'], $req['second_name'], $req['last_name'], $req['mothers_last_name']]));
            $employees[$key]['identity_card'] = $req['identity_card'];
            $req = collect(json_decode(file_get_contents(implode('/', [env("RRHH_URL"), 'city', $req['city_identity_card_id']])), true));
            $employees[$key]['identity_card'] .= ' ' . $req['shortened'];
        }
        $date = Carbon::now();
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => []
            ],
            'employees' => $employees,
            'title' => $procedure_modality->name,
            'loan' => $loan,
            'lenders' => $lenders
        ];
        $file_name = implode('_', ['contrato', $procedure_modality->shortened, $id]) . '.pdf';
        $view = view()->make('loan.contracts.advance')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'letter', $request->copies ?? 1);
        return $view;
    }

    /**
    * Impresión de Formulario de solicitud
    * Devuelve el pdf del Formulario de solicitud acorde a un ID de préstamo
    * @urlParam id required ID del préstamo. Example: 1
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    */
    public function print_form(Request $request, $id, $standalone = true)
    {
        $loan = Loan::findOrFail($id);
        $procedure_modality = $loan->modality;
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            array_push($lenders, self::verify_spouse_disbursable($lender)->disbursable);
        }
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
            'title' => 'Ref. : SOLICITUD DE PRÉSTAMO '.$procedure_modality->procedure_type->second_name,
            'loan' => $loan,
            'lenders' => $lenders
        ];
        $file_name = implode('_', ['formulario', 'solicitud_prestamo']) . '.pdf';
        $view = view()->make('loan.forms.request_form')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'letter', $request->copies ?? 1);
        return $view;
    }
}
