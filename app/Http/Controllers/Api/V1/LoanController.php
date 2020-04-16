<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Auth;
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
use App\Role;
use App\RoleSequence;
use App\Http\Requests\LoanForm;
use App\Http\Requests\LoanPaymentForm;
use App\Http\Requests\LoanObservationForm;
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
    * @queryParam role_id Ver préstamos del rol, si es 0 se muestra la lista completa. Example: 2
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
    *     "first_page_url": "http://127.0.0.1/api/v1/loan?page=1",
    *     "from": 1,
    *     "last_page": 1,
    *     "last_page_url": "http://127.0.0.1/api/v1/loan?page=1",
    *     "next_page_url": null,
    *     "path": "http://127.0.0.1/api/v1/loan",
    *     "per_page": 10,
    *     "prev_page_url": null,
    *     "to": 3,
    *     "total": 3
    * }
    */
    public function index(Request $request)
    {
        $filters = [];
        if (!$request->has('role_id')) {
            if (Auth::user()->can('show-all-loan')) {
                $request->role_id = 0;
            } else {
                $role = Auth::user()->roles()->whereHas('module', function($query) {
                    return $query->whereName('prestamos');
                })->orderBy('sequence_number')->orderBy('name')->first();
                if ($role) {
                    $request->role_id = $role->id;
                } else {
                    abort(403);
                }
            }
        } else {
            $request->role_id = (integer)$request->role_id;
            if (($request->role_id == 0 && !Auth::user()->can('show-all-loan')) || ($request->role_id != 0 && !Auth::user()->roles->pluck('id')->contains($request->role_id))) {
                abort(403);
            }
        }
        if ($request->role_id != 0) {
            $filters = [
                'role_id' => $request->role_id
            ];
        }
        $data = Util::search_sort(new Loan(), $request, $filters);
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
    * @bodyParam personal_reference_id integer ID de referencia personal. Example: 4
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam loan_destiny_id integer required ID destino de Préstamo. Example: 1
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
    *   "loan_destiny_id": 1,
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
    *   "personal_reference_id": 4,
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
        $roles = Auth::user()->roles()->whereHas('module', function($query) {
            return $query->whereName('prestamos');
        })->pluck('id');
        $procedure_modality = ProcedureModality::findOrFail($request->procedure_modality_id);
        $request->merge([
            'role_id' => $procedure_modality->procedure_type->workflow->pluck('role_id')->intersect($roles)->first()
        ]);
        if (!$request->role_id) abort(403);
        // Guardar préstamo
        $saved = $this->save_loan($request);
        // Relacionar afiliados y garantes
        $loan = $saved->loan;
        $request = $saved->request;
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
        if ($request->has('notes')) {
            foreach ($request->notes as $message) {
                $loan->notes()->create([
                    'message' => $message,
                    'date' => Carbon::now()
                ]);
            }
        }
        // Generar PDFs
        $file_name = implode('_', ['solicitud', 'prestamo', $loan->code]) . '.pdf';
        if(Auth::user()->can('print-contract-loan')){
            $loan->attachment = Util::pdf_to_base64([
                $this->print_form(new Request([]), $loan->id, false),
                $this->print_contract(new Request([]), $loan->id, false)
            ], $file_name, 'legal', $request->copies ?? 1);
        }else{
            $loan->attachment = Util::pdf_to_base64([
                $this->print_form(new Request([]), $loan->id, false),
            ], $file_name, 'legal', $request->copies ?? 1);
        }
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
        if (Auth::user()->can('show-all-loan') || Auth::user()->roles()->whereHas('module', function($query) {
            return $query->whereName('prestamos');
        })->pluck('id')->contains($item->role_id)) {
            $item->lenders;
            $item->guarantors;
            $this->append_data($item);
            return $item;
        } else {
            abort(403);
        }
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
    * @bodyParam personal_reference_id integer ID de referencia personal. Example: 4
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam loan_destiny_id integer required ID destino de Préstamo. Example: 1
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
    *     "personal_reference_id": 4,
    *     "request_date": "2020-03-05",
    *     "amount_requested": 2000,
    *     "city_id": 4,
    *     "loan_interest_id": 1,
    *     "loan_state_id": 1,
    *     "amount_approved": 2000,
    *     "loan_term": 2,
    *     "disbursement_type_id": 1,
    *     "loan_destiny_id": 1,
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
        return $saved->loan;
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
        if (Auth::user()->can(['update-loan', 'create-loan']) && ($request->has('lenders') || $request->has('guarantors'))) {
            $request->lenders = collect($request->has('lenders') ? $request->lenders : [])->unique();
            $request->guarantors = collect($request->has('guarantors') ? $request->guarantors : [])->unique();
            $request->guarantors = $request->guarantors->diff($request->lenders);
            if (!$request->has('disbursable_id')) {
                $disbursable_id = $request->lenders[0];
            } else {
                if (!in_array($request->disbursable_id, $request->lenders)) abort(404);
                $disbursable_id = $request->disbursable_id;
            }
            $disbursable = Affiliate::findOrFail($disbursable_id);
        }
        if ($id) {
            $loan = Loan::findOrFail($id);
            if ($request->has('role_id')) {
                if ($request->role_id != $loan->role_id) {
                    $role = Role::findOrFail($request->role_id);
                    $roles = RoleSequence::flow($loan->modality->procedure_type->id, $loan->role_id);
                    if (($loan->validated && $roles->next->search($request->role_id) === false) || (!$loan->validated && $roles->previous->search($request->role_id) === false)) abort(403, 'Derivación inválida');
                }
            }
            if (Auth::user()->can('update-loan')) {
                $loan->fill(array_merge($request->all(), isset($disbursable) ? (array)self::verify_spouse_disbursable($disbursable) : []));
            } else {
                if ($request->has('validated')) {
                    $loan->validated = $request->validated;
                }
                if ($request->has('role_id')) {
                    if ($request->role_id != $loan->role_id) {
                        $loan->role()->associate($role);
                        $loan->validated = false;
                    }
                }
            }
        } else {
            $loan = new Loan(array_merge($request->all(), (array)self::verify_spouse_disbursable($disbursable), ['amount_approved' => $request->amount_requested]));
        }
        $loan->save();
        if (Auth::user()->can(['update-loan', 'create-loan']) && ($request->has('lenders') || $request->has('guarantors'))) {
            $percentage = Loan::get_percentage($request->lenders);
            $affiliates = [];
            foreach ($request->lenders as $affiliate) {
                $affiliates[$affiliate] = [
                    'payment_percentage' => $percentage,
                    'guarantor' => false
                ];
            }
            if($request->guarantors){
                $percentage = Loan::get_percentage($request->guarantors);
                foreach ($request->guarantors as $affiliate) {
                    $affiliates[$affiliate] = [
                        'payment_percentage' => $percentage,
                        'guarantor' => true
                    ];
                }
            }
            if (count($affiliates) > 0) $loan->loan_affiliates()->sync($affiliates);
        }
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
    * {
    *     "content": "zMTcgM....",
    *     "type": "pdf",
    *     "file_name": "contrato_anticipo_17.pdf"
    * }
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
            try {
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
            } catch (\Exception $e) {
                $employees[$key]['name'] = $employees[$key]['identity_card'] = '_______________';
            }
        }
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => []
            ],
            'employees' => $employees,
            'title' => $procedure_modality->name,
            'loan' => $loan,
            'lenders' => collect($lenders)
        ];
        $file_name = implode('_', ['contrato', $procedure_modality->shortened, $loan->code]) . '.pdf';
        $view = view()->make('loan.contracts.advance')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'legal', $request->copies ?? 1);
        return $view;
    }

    /**
    * Impresión de Formulario de solicitud
    * Devuelve el pdf del Formulario de solicitud acorde a un ID de préstamo
    * @urlParam id required ID del préstamo. Example: 1
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @response
    * {
    *     "content": "zMTcgM....",
    *     "type": "pdf",
    *     "file_name": "solicitud_prestamo_17.pdf"
    * }
    */
    public function print_form(Request $request, $id, $standalone = true)
    {
        $loan = Loan::findOrFail($id);
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            array_push($lenders, self::verify_spouse_disbursable($lender)->disbursable);
        }
        $persons = collect([]);
        foreach ($lenders as $lender) {
            $persons->push([
                'id' => $lender->id,
                'full_name' => implode(' ', [$lender->title, $lender->full_name]),
                'identity_card' => $lender->identity_card_ext,
                'position' => 'SOLICITANTE'
            ]);
        }
        foreach ($loan->guarantors as $guarantor) {
            $persons->push([
                'id' => $lender->id,
                'full_name' => implode(' ', [$guarantor->title, $guarantor->full_name]),
                'identity_card' => $guarantor->identity_card_ext,
                'position' => 'GARANTE'
            ]);
        }
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Tipo', $loan->modality->procedure_type->second_name],
                    ['Modalidad', $loan->modality->shortened],
                    ['Usuario', Auth::user()->username ?? 'prueba']
                ]
            ],
            'title' => 'SOLICITUD DE ' . ($loan->parent_loan ? $loan->parent_reason : 'PRÉSTAMO'),
            'loan' => $loan,
            'lenders' => collect($lenders),
            'signers' => $persons
        ];
        $file_name = implode('_', ['solicitud', 'prestamo', $loan->code]) . '.pdf';
        $view = view()->make('loan.forms.request_form')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'legal', $request->copies ?? 1);
        return $view;
    }

    /**
    * Impresión del plan de pagos
    * Devuelve un pdf del plan de pagos acorde a un ID de préstamo
    * @urlParam id required ID del préstamo. Example: 6
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @response
    * {
    *     "content": "zMTcgM....",
    *     "type": "pdf",
    *     "file_name": "plan_anticipo_17.pdf"
    * }
    */
    public function print_plan(Request $request, $id, $standalone = true)
    {
        $loan = Loan::findOrFail($id);
        $procedure_modality = $loan->modality;
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            $lenders[] = self::verify_spouse_disbursable($lender)->disbursable;
        }
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Tipo', $loan->modality->procedure_type->second_name],
                    ['Modalidad', $loan->modality->shortened],
                    ['Usuario', Auth::user()->username ?? 'prueba']
                ]
            ],
            'title' => 'PLAN DE PAGOS',
            'loan' => $loan,
            'lenders' => collect($lenders)
        ];
        $file_name = implode('_', ['plan', $procedure_modality->shortened, $loan->code]) . '.pdf';
        $view = view()->make('loan.payment_plan')->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'legal', $request->copies ?? 1);
        return $view;
    }

    /**
    * Notas aclaratorias
    * Devuelve la lista de notas relacionadas con el préstamo
    * @urlParam id required ID de préstamo. Example: 2
    * @authenticated
    * @response
    * [
    *     {
    *         "id": 15,
    *         "annotable_id": 6,
    *         "annotable_type": "loans",
    *         "message": "BOLETA DE MAYO 2018",
    *         "date": "2018-07-21 11:50:14",
    *         "created_at": "2018-07-21 11:50:14",
    *         "updated_at": "2018-07-21 11:50:14"
    *     }, {}
    * ]
    */
    public function get_notes($id)
    {
        $loan = Loan::findOrFail($id);
        return $loan->notes;
    }

    /**
    * Flujo de trabajo
    * Devuelve la lista de roles anteriores para devolver o posteriores para derivar el trámite
    * @urlParam id required ID de préstamo. Example: 2
    * @authenticated
    * @response
    * {
    *     "current": 73,
    *     "previous": [
    *         75,
    *         76
    *     ],
    *     "next": [
    *         79,
    *         80
    *     ]
    * }
    */
    public function get_flow($id)
    {
        $loan = Loan::findOrFail($id);
        return response()->json(RoleSequence::flow($loan->modality->procedure_type->id, $loan->role_id));
    }

    /** @group Cobranzas
    * Cálculo de siguiente pago
    * Devuelve el número de cuota, días calculados, días de interés que alcanza a pagar con la cuota, días restantes por pagar, montos de interés, capital y saldo a capital.
    * @urlParam id required ID de préstamo. Example: 2
    * @bodyParam estimated_date date Fecha para el cálculo del interés. Example: 2020-04-15
    * @bodyParam estimated_quota float Monto para el cálculo. Example: 650
    * @authenticated
    * @response
    * {
    *     "estimated_date": "2020-04-15",
    *     "quota_number": 1,
    *     "estimated_days": {
    *         "penal": 173,
    *         "accumulated": 173,
    *         "current": 15
    *     },
    *     "paid_days": {
    *         "penal": 173,
    *         "accumulated": 97,
    *         "current": 0
    *     },
    *     "balance": 2000,
    *     "capital_payment": 1.78,
    *     "interest_payment": 0,
    *     "accumulated_payment": 191.34,
    *     "penal_payment": 56.88,
    *     "estimated_quota": 250,
    *     "next_balance": 1998.22,
    *     "penal_remaining": 0,
    *     "accumulated_remaining": 91
    * }
    */
    public function get_next_payment(LoanPaymentForm $request, $id)
    {
        $loan = Loan::findOrFail($id);
        return $loan->next_payment($request->input('estimated_date', null), $request->input('estimated_quota', null));
    }

    /** @group Cobranzas
    * Nuevo pago
    * Inserta una cuota de acuerdo a un monto y fecha estimados.
    * @urlParam id required ID de préstamo. Example: 2
	* @bodyParam estimated_date date Fecha para el cálculo del interés. Example: 2020-04-30
	* @bodyParam estimated_quota float Monto para el cálculo de los días de interés pagados. Example: 600
	* @bodyParam affiliate_id integer ID de afiliado que realizó el pago. Example: 56
	* @bodyParam payment_type_id integer required ID de tipo de pago. Example: 2
	* @bodyParam voucher_number integer Número de boleta de depósito. Example: 65100
	* @bodyParam receipt_number integer Número de recibo. Example: 102
	* @bodyParam description string Texto de descripción. Example: Penalizacion regularizada
    * @authenticated
    * @response
    * {
    *     "estimated_date": "2020-04-30",
    *     "quota_number": 2,
    *     "estimated_days": {
    *         "penal": 91,
    *         "accumulated": 91,
    *         "current": 15
    *     },
    *     "paid_days": {
    *         "penal": 91,
    *         "accumulated": 91,
    *         "current": 15
    *     },
    *     "balance": 1998.22,
    *     "capital_payment": 361.2,
    *     "interest_payment": 29.56,
    *     "accumulated_payment": 179.35,
    *     "penal_payment": 29.89,
    *     "estimated_quota": 600,
    *     "next_balance": 1637.02,
    *     "penal_remaining": 0,
    *     "accumulated_remaining": 0,
    *     "payment_type_id": 2,
    *     "pay_date": "2020-04-05T00:48:52.015535Z",
    *     "affiliate_id": 47352,
    *     "voucher_number": 65100,
    *     "receipt_number": 102,
    *     "description": "Penalización regularizado"
    * }
    */
    public function set_payment(LoanPaymentForm $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $payment = $loan->next_payment($request->input('estimated_date', null), $request->input('estimated_quota', null));
        $payment->payment_type_id = $request->payment_type_id;
        $payment->pay_date = Carbon::now();
        $payment->affiliate_id = $request->input('affiliate_id', $loan->disbursable_id);
        $payment->voucher_number = $request->input('voucher_number', null);
        $payment->receipt_number = $request->input('receipt_number', null);
        $payment->description = $request->input('description', null);
        $loan->payments()->create($payment->toArray());
        return $payment;
    }

    /** @group Cobranzas
    * Lista de pagos
    * Devuelve el listado de los pagos ordenados por cuota de manera descendente
    * @urlParam id required ID de préstamo. Example: 2
    * @authenticated
    * @response
    * [
    *     {
    *         "loan_id": 2,
    *         "affiliate_id": 47352,
    *         "pay_date": "2020-04-04",
    *         "estimated_date": "2020-04-30",
    *         "quota_number": 2,
    *         "estimated_quota": "600",
    *         "penal_payment": "29.89",
    *         "accumulated_payment": "179.35",
    *         "interest_payment": "29.56",
    *         "capital_payment": "361.2",
    *         "penal_remaining": "0",
    *         "accumulated_remaining": "0",
    *         "voucher_number": null,
    *         "payment_type_id": 2,
    *         "receipt_number": null,
    *         "description": null,
    *         "created_at": "2020-04-04 20:48:52",
    *         "updated_at": "2020-04-04 20:48:52"
    *     }, {}
    * ]
    */
    public function get_payments($id)
    {
        $loan = Loan::findOrFail($id);
        return $loan->payments;
    }

    /** @group Observaciones de Préstamos
    * Lista de observaciones
    * Devuelve el listado de los pagos ordenados por cuota de manera descendente
    * @urlParam id required ID de préstamo. Example: 2
    * @authenticated
    * @response
    * [
    *     {
    *         "user_id": 123,
    *         "observation_type_id": 2,
    *         "message": "Subsanable en una semana",
    *         "date": "2020-04-14 21:16:52",
    *         "enabled": false,
    *         "created_at": "2020-04-15T01:16:52.000000Z",
    *         "updated_at": "2020-04-15T01:16:52.000000Z",
    *         "deleted_at": null
    *     }, {}
    * ]
    */
    public function get_observations($id)
    {
        $loan = Loan::findOrFail($id);
        return $loan->observations;
    }

    /** @group Observaciones de Préstamos
    * Nueva observación
    * Inserta una nueva observación asociada al trámite
    * @urlParam id required ID de préstamo. Example: 2
    * @bodyParam observation_type_id integer required ID de tipo de observación. Example: 2
    * @bodyParam message string Mensaje adjunto a la observación. Example: Subsanable en una semana
    * @authenticated
    * @response
    * {
    *     "message": "Subsanable en una semana",
    *     "observation_type_id": 2,
    *     "date": "2020-04-15T01:16:52.886784Z",
    *     "user_id": 123,
    *     "updated_at": "2020-04-15T01:16:52.000000Z",
    *     "created_at": "2020-04-15T01:16:52.000000Z",
    *     "user": {
    *         "id": 123,
    *         "city_id": 4,
    *         "first_name": "Nelvis Irene",
    *         "last_name": "Alarcon",
    *         "username": "nalarcon",
    *         "created_at": "2019-04-22T19:27:44.000000Z",
    *         "updated_at": "2020-04-15T01:08:09.000000Z",
    *         "position": "Encargada de Registro, Control y Recuperacion de Prestamos",
    *         "is_commission": false,
    *         "phone": null,
    *         "active": true
    *     }
    * }
    */
    public function set_observation(LoanObservationForm $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $observation = $loan->observations()->make([
            'message' => $request->message ?? null,
            'observation_type_id' => $request->observation_type_id,
            'date' => Carbon::now()
        ]);
        $observation->user()->associate(Auth::user());
        $observation->save();
        return $observation;
    }

    /** @group Observaciones de Préstamos
    * Actualizar observación
    * Actualiza los datos de una observación asociada al trámite
    * @urlParam id required ID de préstamo. Example: 2
    * @bodyParam original.user_id integer required ID de usuario que creó la observación. Example: 123
    * @bodyParam original.observation_type_id integer required ID de tipo de observación original. Example: 2
    * @bodyParam original.message string required Mensaje de la observación original. Example: Subsanable en una semana
    * @bodyParam original.date date required Fecha de la observación original. Example: 2020-04-14 21:16:52
    * @bodyParam original.enabled boolean required Estado de la observación original. Example: false
    * @bodyParam update.observation_type_id integer ID de tipo de observación a actualizar. Example: 21
    * @bodyParam update.message string Mensaje de la observación a actualizar. Example: Subsanable en un mes
    * @bodyParam update.enabled boolean Estado de la observación a actualizar. Example: true
    * @authenticated
    * @response
    * [
    *     {
    *         "user_id": 123,
    *         "observation_type_id": 21,
    *         "message": "Subsanable en un mes",
    *         "date": "2020-04-14 21:16:52",
    *         "enabled": true,
    *         "created_at": "2020-04-15T01:16:52.000000Z",
    *         "updated_at": "2020-04-15T02:34:26.000000Z",
    *         "deleted_at": null
    *     }, {}
    * ]
    */
    public function update_observation(LoanObservationForm $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $observation = $loan->observations();
        foreach (collect($request->original)->except('created_at', 'updated_at', 'deleted_at') as $key => $value) {
            $observation = $observation->where($key, $value);
        }
        $observation->update(collect($request->update)->only('observation_type_id', 'message', 'enabled')->toArray());
        return $loan->observations;
    }
}
