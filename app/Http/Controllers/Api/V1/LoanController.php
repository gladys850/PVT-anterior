<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Affiliate;
use App\City;
use App\User;
use App\Loan;
use App\Tag;
use App\LoanState;
use App\RecordType;
use App\ProcedureDocument;
use App\ProcedureModality;
use App\PaymentType;
use App\Role;
use App\RoleSequence;
use App\Http\Requests\LoansForm;
use App\Http\Requests\LoanForm;
use App\Http\Requests\LoanPaymentForm;
use App\Http\Requests\ObservationForm;
use App\Events\LoanFlowEvent;
use Carbon;
use Util;

/** @group Préstamos
* Datos de los trámites de préstamos y sus relaciones
*/
class LoanController extends Controller
{
    public static function append_data(Loan $loan, $with_lenders = false)
    {
        $loan->payable_liquid_calculated = $loan->payable_liquid_calculated;
        $loan->bonus_calculated = $loan->bonus_calculated;
        $loan->indebtedness_calculated = $loan->indebtedness_calculated;
        $loan->liquid_qualification_calculated = $loan->liquid_qualification_calculated;
        $loan->balance = $loan->balance;
        $loan->estimated_quota = $loan->estimated_quota;
        $loan->defaulted = $loan->defaulted;
        $loan->observed = $loan->observed;
        if ($with_lenders) {
            $loan->lenders = $loan->lenders;
            $loan->guarantors = $loan->guarantors;
        }
        return $loan;
    }

    /**
    * Lista de Préstamos
    * Devuelve el listado con los datos paginados
    * @queryParam role_id Ver préstamos del rol, si es 0 se muestra la lista completa. Example: 73
    * @queryParam trashed Booleano para obtener solo eliminados. Example: 1
    * @queryParam validated Booleano para filtrar trámites válidados. Example: 1
    * @queryParam procedure_type_id ID para filtrar trámites por tipo de trámite. Example: 9
    * @queryParam search Parámetro de búsqueda. Example: 2000
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/loan/index.200.json
    */
    public function index(Request $request)
    {
        $filters = [];
        $relations = [];
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
            if(!Auth::user()->can('show-all-loan')){
                if($request->has('trashed') && !Auth::user()->can('show-deleted-loan')) abort(403);
            }
            $filters = [
                'role_id' => $request->role_id
            ];
        }
        if ($request->has('validated')) $filters['validated'] = $request->boolean('validated');
        if ($request->has('procedure_type_id')) {
            $relations['modality'] = [
                'procedure_type_id' => $request->procedure_type_id
            ];
        }
        $data = Util::search_sort(new Loan(), $request, $filters, $relations);
        $data->getCollection()->transform(function ($loan) {
            return self::append_data($loan, false);
        });
        return $data;
    }

    /**
    * Nuevo préstamo
    * Inserta nuevo préstamo
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 32
    * @bodyParam amount_requested integer required monto solicitado. Example: 2000
    * @bodyParam city_id integer required ID de la ciudad. Example: 3
    * @bodyParam loan_term integer required plazo. Example: 2
    * @bodyParam payment_type_id integer required Tipo de desembolso. Example: 1
    * @bodyParam lenders array required Lista de IDs de afiliados Titular de préstamo. Example: [5146]
    * @bodyParam payable_liquid_calculated numeric required Promedio liquido pagable. Example: 2000
    * @bodyParam bonus_calculated integer required Total de bono calculado. Example: 24
    * @bodyParam liquid_qualification_calculated numeric required Total de bono calculado. Example: 2000
    * @bodyParam indebtedness_calculated numeric required Indice de endeudamiento. Example: 52.26
    * @bodyParam guarantors array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (REFINANCIAMIENTO, REPROGRAMACIÓN) Tipo de trámite hijo. Example: REFINANCIAMIENTO
    * @bodyParam personal_reference_id integer ID de referencia personal. Example: 4
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam destiny_id integer required ID destino de Préstamo. Example: 1
    * @bodyParam documents array required Lista de IDs de Documentos solicitados. Example: [40, 271, 273, 274]
    * @bodyParam notes array Lista de notas aclaratorias. Example: [Informe de baja policial, Carta de solicitud]
    * @authenticated
    * @responseFile responses/loan/store.200.json
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
        if (!$request->role_id) abort(403, 'Debe crear un flujo de trabajo');
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
                $this->print_form(new Request([]), $loan, false),
                $this->print_contract(new Request([]), $loan, false)
            ], $file_name, 'legal', $request->copies ?? 1);
        }else{
            $loan->attachment = Util::pdf_to_base64([
                $this->print_form(new Request([]), $loan, false),
            ], $file_name, 'legal', $request->copies ?? 1);
        }
        return $loan;
    }

    /**
    * Detalle de Préstamo
    * Devuelve el detalle de un préstamo mediante su ID
    * @urlParam loan required ID de préstamo. Example: 4
    * @authenticated
    * @responseFile responses/loan/show.200.json
    */
    public function show(Loan $loan)
    {
        if (Auth::user()->can('show-all-loan') || Auth::user()->roles()->whereHas('module', function($query) {
            return $query->whereName('prestamos');
        })->pluck('id')->contains($loan->role_id)) {
            return self::append_data($loan, true);
        } else {
            abort(403);
        }
    }

    /**
    * Actualizar préstamo
    * Actualizar datos principales de préstamo
    * @urlParam loan required ID del préstamo. Example: 1
    * @bodyParam procedure_modality_id integer required ID de modalidad. Example: 32
    * @bodyParam amount_requested integer required monto solicitado. Example: 2000
    * @bodyParam city_id integer required ID de la ciudad. Example: 4
    * @bodyParam loan_term integer required plazo. Example: 2
    * @bodyParam payment_type_id integer required Tipo de desembolso. Example: 1
    * @bodyParam lenders array required Lista de IDs de afiliados Titular de préstamo. Example: [5146]
    * @bodyParam payable_liquid_calculated numeric required Promedio liquido pagable. Example: 2000
    * @bodyParam bonus_calculated integer required Total de bono calculado. Example: 24
    * @bodyParam liquid_qualification_calculated numeric required Total de bono calculado. Example: 2000
    * @bodyParam indebtedness_calculated numeric required Indice de endeudamiento. Example: 52.26
    * @bodyParam guarantors array Lista de IDs de afiliados Garante de préstamo. Example: []
    * @bodyParam disbursement_date date Fecha de desembolso. Example: 2020-02-01
    * @bodyParam parent_loan_id integer ID de Préstamo Padre. Example: 1
    * @bodyParam parent_reason enum (REFINANCIAMIENTO, REPROGRAMACIÓN) Tipo de trámite hijo. Example: REFINANCIAMIENTO
    * @bodyParam personal_reference_id integer ID de referencia personal. Example: 4
    * @bodyParam account_number integer Número de cuenta en Banco Union. Example: 586621345
    * @bodyParam destiny_id integer required ID destino de Préstamo. Example: 1
    * @bodyParam role_id integer Rol al cual derivar o devolver. Example: 81
    * @bodyParam validated boolean Estado validación del préstamo. Example: true
    * @authenticated
    * @responseFile responses/loan/update.200.json
    */
    public function update(LoanForm $request, Loan $loan)
    {
        $saved = $this->save_loan($request, $loan);
        return $saved->loan;
    }

    /**
    * Anular préstamo
    * @urlParam loan required ID del préstamo. Example: 1
    * @authenticated
    * @responseFile responses/loan/destroy.200.json
    */
    public function destroy(Loan $loan)
    {
        $state = LoanState::whereName('Anulado')->first();
        $loan->state()->associate($state);
        $loan->save();
        $loan->delete();
        return $loan;
    }

    private function save_loan(Request $request, $loan = null)
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
        if ($loan) {
            $exceptions = ['code', 'role_id'];
            if ($request->has('validated')) {
                if (!Auth::user()->roles()->pluck('id')->contains($loan->role_id)) {
                    array_push($exceptions, 'validated');
                }
            }
            if (Auth::user()->can('update-loan')) {
                $loan->fill(array_merge($request->except($exceptions), isset($disbursable) ? (array)self::verify_spouse_disbursable($disbursable) : []));
            }
            if (!in_array('validated', $exceptions)) $loan->validated = $request->validated;
            if ($request->has('role_id')) {
                if ($request->role_id != $loan->role_id) {
                    $loan->role()->associate(Role::find($request->role_id));
                    $loan->validated = false;
                    event(new LoanFlowEvent([$loan]));
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
    * Actualización de documentos
    * Actualiza los datos para cada documento presentado
    * @urlParam loan required ID del préstamo. Example: 8
    * @urlParam document required ID de préstamo. Example: 40
    * @bodyParam is_valid boolean required Validez del documento. Example: true
    * @bodyParam comment string Comentario para añadir a la presentación. Example: Documento actualizado a la gestión actual
    * @authenticated
    * @responseFile responses/loan/update_document.200.json
    */
    public function update_document(Request $request, Loan $loan, ProcedureDocument $document)
    {
        $request->validate([
            'is_valid' => 'required|boolean',
            'comment' => 'string|nullable|min:1'
        ]);
        $loan->submitted_documents()->updateExistingPivot($document->id, $request->all());
        return $loan->submitted_documents;
    }

    /**
    * Lista de documentos entregados
    * Obtiene la lista de los documentos presentados para el trámite
    * @urlParam loan required ID del préstamo. Example: 8
    * @authenticated
    * @responseFile responses/loan/get_documents.200.json
    */
    public function get_documents(Loan $loan)
    {
        return $loan->submitted_documents_list;
    }

    /**
    * Desembolso Afiliado
    * Devuelve los datos del o la cónyugue en caso de que hubiera fallecido a quien se hace el desembolso del préstamo
    * @urlParam loan required ID del préstamo. Example: 2
    * @authenticated
    * @responseFile responses/loan/get_disbursable.200.json
    */
    public function get_disbursable(Loan $loan)
    {
        return $loan->disbursable;
    }

    public static function verify_spouse_disbursable(Affiliate $affiliate)
    {
        $object = (object)[
            'disbursable_type' => 'affiliates',
            'disbursable_id' => $affiliate->id,
            'disbursable' => $affiliate
        ];
        if ($object->disbursable->dead) {
            $spouse = $object->disbursable->spouse;
            if ($spouse) {
                $object = (object)[
                    'disbursable_type' => 'spouses',
                    'disbursable_id' => $spouse->id,
                    'disbursable' => $spouse
                ];
            } else {
                abort(409, 'Debe actualizar la información de cónyugue para afiliados fallecidos');
            }
        }
        $needed_keys = ['city_birth', 'city_identity_card', 'city_identity_card'];
        foreach ($needed_keys as $key) {
            if (!$object->disbursable[$key]) abort(409, 'Debe actualizar los datos personales del titular y garantes');
        }
        return $object;
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
    * @urlParam loan required ID del préstamo. Example: 6
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @responseFile responses/loan/print_contract.200.json
    */
    public function print_contract(Request $request, Loan $loan, $standalone = true)
    {
        $procedure_modality = $loan->modality;
        $parent_loan = "";
        if($loan->parent_loan_id) $parent_loan = Loan::findOrFail($loan->parent_loan_id);
        $lenders = [];
        foreach ($loan->lenders as $lender) {
            $lenders[] = self::verify_spouse_disbursable($lender);
        }
        $guarantors = [];
        foreach ($loan->guarantors as $guarantor) {
            $guarantors[] = $guarantor;
        }
        $employees = [
            ['position' => 'Director General Ejecutivo'],
            ['position' => 'Director de Asuntos Administrativos']
        ];
        foreach ($employees as $key => $employee) {
            $employees[$key] = Util::request_rrhh_employee($employee['position']);
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
            'lenders' => collect($lenders),
            'guarantors' => collect($guarantors),
            'parent_loan' => $parent_loan
        ];
        $file_name = implode('_', ['contrato', $procedure_modality->shortened, $loan->code]) . '.pdf';
        $modality_type = $procedure_modality->procedure_type->name;
        switch($modality_type){
            case 'Préstamo Anticipo':
				$view_type = 'advance';
            	break;
            case 'Préstamo a corto plazo':
				$view_type = 'short';
            	break;
            case 'Préstamo a largo plazo':
				$view_type = 'long';
            	break;
            case 'Préstamo hipotecario':
				$view_type = 'hypothecary';
            	break;
        }
		$view = view()->make('loan.contracts.' . $view_type)->with($data)->render();
        if ($standalone) return Util::pdf_to_base64([$view], $file_name, 'legal', $request->copies ?? 1);
        return $view;
    }

    /**
    * Impresión de Formulario de solicitud
    * Devuelve el pdf del Formulario de solicitud acorde a un ID de préstamo
    * @urlParam loan required ID del préstamo. Example: 1
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @responseFile responses/loan/print_form.200.json
    */
    public function print_form(Request $request, Loan $loan, $standalone = true)
    {
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
                    ['Usuario', Auth::user()->username]
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
    * @urlParam loan required ID del préstamo. Example: 6
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @responseFile responses/loan/print_plan.200.json
    */
    public function print_plan(Request $request, Loan $loan, $standalone = true)
    {
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
                    ['Usuario', Auth::user()->username]
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
    * Lista de Notas aclaratorias
    * Devuelve la lista de notas relacionadas con el préstamo
    * @urlParam loan required ID del préstamo. Example: 2
    * @authenticated
    * @responseFile responses/loan/get_notes.200.json
    */
    public function get_notes(Loan $loan)
    {
        return $loan->notes;
    }

    /**
    * Flujo de trabajo
    * Devuelve la lista de roles anteriores para devolver o posteriores para derivar el trámite
    * @urlParam loan required ID del préstamo. Example: 2
    * @authenticated
    * @responseFile responses/loan/get_flow.200.json
    */
    public function get_flow(Loan $loan)
    {
        return response()->json(RoleSequence::flow($loan->modality->procedure_type->id, $loan->role_id));
    }

    /** @group Cobranzas
    * Cálculo de siguiente pago
    * Devuelve el número de cuota, días calculados, días de interés que alcanza a pagar con la cuota, días restantes por pagar, montos de interés, capital y saldo a capital.
    * @urlParam loan required ID del préstamo. Example: 2
    * @bodyParam estimated_date date Fecha para el cálculo del interés. Example: 2020-04-15
    * @bodyParam estimated_quota float Monto para el cálculo. Example: 650
    * @bodyParam liquidate boolean Booleano para hacer el cálculo con el monto máximo que liquidará el préstamo. Example: false
    * @authenticated
    * @responseFile responses/loan/get_next_payment.200.json
    */
    public function get_next_payment(LoanPaymentForm $request, Loan $loan)
    {
        return $loan->next_payment($request->input('estimated_date', null), $request->input('estimated_quota', null), $request->input('liquidate', false));
    }

    /** @group Cobranzas
    * Nuevo pago
    * Inserta una cuota de acuerdo a un monto y fecha estimados.
    * @urlParam loan required ID del préstamo. Example: 2
	* @bodyParam estimated_date date Fecha para el cálculo del interés. Example: 2020-04-30
	* @bodyParam estimated_quota float Monto para el cálculo de los días de interés pagados. Example: 600
	* @bodyParam affiliate_id integer ID de afiliado que realizó el pago. Example: 56
	* @bodyParam payment_type_id integer required ID de tipo de pago. Example: 2
	* @bodyParam voucher_number integer Número de boleta de depósito. Example: 65100
	* @bodyParam receipt_number integer Número de recibo. Example: 102
	* @bodyParam description string Texto de descripción. Example: Penalizacion regularizada
    * @authenticated
    * @responseFile responses/loan/set_payment.200.json
    */
    public function set_payment(LoanPaymentForm $request, Loan $loan)
    {
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
    * @urlParam loan required ID del préstamo. Example: 2
    * @authenticated
    * @responseFile responses/loan/get_payments.200.json
    */
    public function get_payments(Loan $loan)
    {
        return $loan->payments;
    }

    /** @group Observaciones de Préstamos
    * Lista de observaciones
    * Devuelve el listado de observaciones del trámite
    * @urlParam loan required ID del préstamo. Example: 2
    * @queryParam trashed Booleano para obtener solo observaciones eliminadas. Example: 1
    * @authenticated
    * @responseFile responses/loan/get_observations.200.json
    */
    public function get_observations(Request $request, Loan $loan)
    {
        $query = $loan->observations();
        if ($request->boolean('trashed')) $query = $query->onlyTrashed();
        return $query->get();
    }

    /** @group Observaciones de Préstamos
    * Nueva observación
    * Inserta una nueva observación asociada al trámite
    * @urlParam loan required ID del préstamo. Example: 2
    * @bodyParam observation_type_id integer required ID de tipo de observación. Example: 2
    * @bodyParam message string required Mensaje adjunto a la observación. Example: Subsanable en una semana
    * @authenticated
    * @responseFile responses/loan/set_observation.200.json
    */
    public function set_observation(ObservationForm $request, Loan $loan)
    {
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
    * @urlParam loan required ID del préstamo. Example: 2
    * @bodyParam original.user_id integer required ID de usuario que creó la observación. Example: 123
    * @bodyParam original.observation_type_id integer required ID de tipo de observación original. Example: 2
    * @bodyParam original.message string required Mensaje de la observación original. Example: Subsanable en una semana
    * @bodyParam original.date date required Fecha de la observación original. Example: 2020-04-14 21:16:52
    * @bodyParam original.enabled boolean required Estado de la observación original. Example: false
    * @bodyParam update.enabled boolean Estado de la observación a actualizar. Example: true
    * @authenticated
    * @responseFile responses/loan/update_observation.200.json
    */
    public function update_observation(ObservationForm $request, Loan $loan)
    {
        $observation = $loan->observations();
        foreach (collect($request->original)->only('user_id', 'observation_type_id', 'message', 'date', 'enabled')->put('observable_id', $loan->id)->put('observable_type', 'loans') as $key => $value) {
            $observation = $observation->where($key, $value);
        }
        if ($observation->count() === 1) {
            $obs = $observation->first();
            if (isset($request->update['enabled'])) {
                if ($request->update['enabled']) {
                    $message = 'subsanó observación: ';
                } else {
                    $message = 'observó: ';
                }
            } else {
                $message = 'modificó observación: ';
            }
            Util::save_record($obs, 'observaciones', $message . $obs->message, $obs->observable);
            $observation->update(collect($request->update)->only('observation_type_id', 'message', 'enabled')->toArray());
        }
        return $loan->observations;
    }

    /** @group Observaciones de Préstamos
    * Eliminar observación
    * Elimina una observación del trámite siempre y cuando no haya sido modificada
    * @urlParam loan required ID del préstamo. Example: 2
    * @bodyParam user_id integer required ID de usuario que creó la observación. Example: 123
    * @bodyParam observation_type_id integer required ID de tipo de observación. Example: 2
    * @bodyParam message string required Mensaje de la observación. Example: Subsanable en una semana
    * @bodyParam date required Fecha de la observación. Example: 2020-04-14 21:16:52
    * @bodyParam enabled boolean required Estado de la observación. Example: false
    * @authenticated
    * @responseFile responses/loan/unset_observation.200.json
    */
    public function unset_observation(ObservationForm $request, Loan $loan)
    {
        $request->request->add(['observable_type' => 'loans', 'observable_id' => $loan->id]);
        $observation = $loan->observations();
        foreach ($request->except('created_at','updated_at','deleted_at') as $key => $value) {
            $observation = $observation->where($key, $value);
        }
        $observation = $observation->whereColumn('created_at','updated_at');
        if ($observation->count() == 1) {
            $observation->delete();
            return $loan->observations;
        } else {
            abort(404, 'La observación fue modificada, no se puede eliminar');
        }
    }

    /**
    * Derivar en lote
    * Deriva o devuelve trámites en un lote mediante sus IDs
    * @bodyParam ids array required Lista de IDs de los trámites a derivar. Example: [1,2,3]
    * @bodyParam role_id integer required ID del rol al cual derivar o devolver. Example: 82
    * @authenticated
    * @responseFile responses/loan/bulk_update_role.200.json
    */
    public function bulk_update_role(LoansForm $request)
    {
        $sequence = null;
        $from_role = null;
        $to_role = $request->role_id;
        $loans = Loan::whereIn('id', $request->ids)->where('role_id', '!=', $request->role_id)->orderBy('code');
        $derived = $loans->get();
        $to_role = Role::find($to_role);
        if (count(array_unique($loans->pluck('role_id')->toArray()))) $from_role = $derived->first()->role_id;
        if ($from_role) {
            $from_role = Role::find($from_role);
            $flow_message = $this->flow_message($derived->first()->modality->procedure_type->id, $from_role, $to_role);
        }
        $derived->map(function ($item, $key) use ($from_role, $to_role, $flow_message) {
            if (!$from_role) {
                $item['from_role_id'] = $item['role_id'];
                $from_role = Role::find($item['role_id']);
                $flow_message = $this->flow_message($item->modality->procedure_type->id, $from_role, $to_role);
            }
            $item['role_id'] = $to_role->id;
            $item['validated'] = false;
            Util::save_record($item, $flow_message['type'], $flow_message['message']);
        });
        $loans->update(array_merge($request->only('role_id'), ['validated' => false]));
        $derived->transform(function ($loan) {
            return self::append_data($loan, false);
        });
        event(new LoanFlowEvent($derived));
        // PDF template
        $data = [
            'type' => 'loan',
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'Área de ' . $from_role->display_name,
                'table' => [
                    ['Fecha', Carbon::now()->isoFormat('L')],
                    ['Hora', Carbon::now()->format('H:i')],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => ($flow_message['type'] == 'derivacion' ? 'DERIVACIÓN' : 'DEVOLUCIÓN') . ' DE TRÁMITES - MODALIDAD ' . $derived->first()->modality->procedure_type->second_name,
            'procedures' => $derived,
            'roles' => [
                'from' => $from_role,
                'to' => $to_role
            ]
        ];
        $file_name = implode('_', ['derivacion', 'prestamos', Str::slug(Carbon::now()->isoFormat('LLL'), '_')]) . '.pdf';
        $view = view()->make('flow.bulk_flow_procedures')->with($data)->render();
        return response()->json([
            'attachment' => Util::pdf_to_base64([$view], $file_name, 'letter', $request->copies ?? 1, false),
            'derived' => $derived
        ]);
    }

    private function flow_message($procedure_type_id, $from_role, $to_role)
    {
        $sequence = RoleSequence::flow($procedure_type_id, $from_role->id);
        if (in_array($to_role->id, $sequence->next->all())) {
            $message = 'derivó';
            $type = 'derivacion';
        } else {
            $message = 'devolvió';
            $type = 'devolucion';
        }
        $message .= ' de ' . $from_role->display_name . ' a ' . $to_role->display_name;
        return [
            'message' => $message,
            'type' => $type
        ];
    }
}
