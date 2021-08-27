<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MovementFundRotatory;
use App\Http\Requests\MovementFundRotatoryForm;
use DB;
use App\Affiliate;
use App\MovementConcept;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Util;
use Carbon\CarbonImmutable;
use Carbon;
use App\Loan;

/** @group Movimientos
* Datos de los movimientos de fondo rotatorio
*/
class MovementFundRotatoryController extends Controller
{
    public static function append_data(MovementFundRotatory $movement_fund_rotatory)
    {
        $movement_fund_rotatory->movement_concept = $movement_fund_rotatory->movement_concept;
        return $movement_fund_rotatory;
    }

    /**
    * Lista de movimientos de fondo rotatorio
    * Devuelve el listado de llos movimientos de fondo rotatorio
    * @authenticated
    * @responseFile responses/movements/index.200.json
    */
    public function index()
    {
        return MovementFundRotatory::orderBy('created_at')->get();
    }


    /**
    * Detalle de un movimiento de fondo rotatorio
    * Devuelve el detalle de un movimiento de fondo rotatorio mediante su ID
    * @urlParam movement required ID de movimiento. Example: 1
    * @authenticated
    * @responseFile responses/movements/show.200.json
    */
    public function show(MovementFundRotatory $movement)
    {
        return $this->append_data($movement);
    }

    /**
    * Edita el Registro del movimiento del fondo rotatorio.
    * @urlParam id required ID del registro realizado. Example: 1
	* @bodyParam date_check_delivery date fecha del cheque. Example: 28-08-2121
    * @bodyParam description string descripcion del movimiento. Example: ingreso de fondo rotatorio
    * @bodyParam entry_amount float monto de ingreso del movimiento. Example: 50000
    * @bodyParam output_amount float monto de egreso del movimiento. Example: 2000
    * @bodyParam user_id integer ID del usuario que registro. Example: 70
    * @bodyParam role_id integer role con el que el registro fue creado. Example: 90
    * @authenticated
    * @responseFile responses/movements/update.200.json
    */
    public function update(MovementFundRotatoryForm $request,$movementfundRotatory_id)
    {
        $movementfundRotatory = MovementFundRotatory::findOrFail($movementfundRotatory_id);
        $movementfundRotatory->fill($request->all());
        $movementfundRotatory->save();
        return  $movementfundRotatory;
    }

    /**
    * Anular Registro de moviento de fondo rotatorio
    * @urlParam movement required ID del registro. Example: 1
    * @authenticated
    * @responseFile responses/movements/destroy.200.json
    */
    public function destroy($movementfundRotatory_id)
    {
        $movementfundRotatory = MovementFundRotatory::findOrFail($movementfundRotatory_id);
        $movementfundRotatory->delete();
        return $movementfundRotatory;
    }


    /**
    * @authenticated
    * @responseFile responses/movements/store.200.json
    */
    /*public function store(FundRotatoryOutputForm $request)
    {
        return FundRotatoryOutput::create($request->all());
    }*/

    public function store_input()
    {}

    public function store_output()
    {}

    /**
    * Impresión del recibo desembolso de prestamo
    * Devuelve un pdf del Pago acorde a un ID de registro de fondo rotatorio
    * @urlParam loan_id required ID del prestamo. Example: 1
    * @queryParam copies Número de copias del documento. Example: 2
    * @authenticated
    * @responseFile responses/movements/print_output_fund_rotatory.200.json
    */
    public function print_fund_rotary(Request $request,$loan_id, $standalone = true)

    {   $movement_fund_rotatorie = MovementFundRotatory::whereLoanId($loan_id)->first();
        $loan = Loan::findOrFail($loan_id);
        $affiliate = Affiliate::findOrFail($loan->affiliate_id);
        $lenders = [];
        $lenders[] = LoanController::verify_loan_affiliates($affiliate,$loan)->disbursable;
        $persons = collect([]);
        foreach ($lenders as $lender) {
            $persons->push([
                'full_name' => implode(' ', [$lender->full_name, $lender->full_name]),
                'identity_card' => $lender->identity_card_ext,
                'position' => 'RECIBIDO POR'
            ]);
        }
        $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ESTRATEGIAS SOCIALES E INVERSIONES',
                'unity' => 'UNIDAD DE INVERSIÓN EN PRÉSTAMOS',
                'table' => [
                    ['Código', $movement_fund_rotatorie->movement_concept_code],
                    ['Fecha', Carbon::now()->format('d/m/Y')],
                    ['Hora', Carbon::now()->format('h:m:i')],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => 'RECIBO DE PAGO',
            'ouputs_fund_rotatorie' => $movement_fund_rotatorie,
            'loan' => $loan,
            'signers' => $persons,
            'lenders' => collect($lenders)
        ];
     
        $file_name = implode('_', ['movement_fund_rotatory', $movement_fund_rotatorie->movement_concept_code]) . '.pdf';
        $view = view()->make('loan.forms.disbursement_receipt_form')->with($data)->render();
        if ($standalone) return Util::pdf_to_treasury_receipt([$view],'letter', $request->copies ?? 1);
        return $view; 
    }

    /**
     * Reportes de salidas fondo rotatorio Prestamos concepto "DESEMBOLSO ANTICIPO"
     * @queryParam initial_date Fecha inicial. Example: 2021-01-01
     * @queryParam final_date Fecha Final. Example: 2021-05-01
     * @responseFile responses/movements/disbursement_receipt_form.200.json
     * @authenticated
     */ 
    public function disbursements_fund_rotatory_outputs_report(request $request, $standalone = true)
    {
        $movement_concept = MovementConcept::whereName('DESEMBOLSO ANTICIPO')->first();
        $initial_date = request('initial_date') ?? '';
        $final_date = request('final_date') ?? '';
        $state_vigente='Vigente';
        $conditions = [];
        //desde aqui
        if ($initial_date != '' && $final_date != '') {
            $date_ini = $request->initial_date.' 00:00:00';
            $date_fin = $request->final_date.' 23:59:59';

            $loans = DB::table('view_loan_borrower')
              ->join('movement_fund_rotatories','movement_fund_rotatories.loan_id','=','view_loan_borrower.id_loan')
              ->whereBetween('movement_fund_rotatories.created_at', [$date_ini, $date_fin])
              ->where("view_loan_borrower.state_loan", "Vigente")
              ->where("movement_fund_rotatories.movement_concept_id",'=',$movement_concept->id)
              ->whereNull("movement_fund_rotatories.deleted_at")
              ->select('*')
              ->orderBy('movement_fund_rotatories.id')
              ->get();

        } else {
            if ($final_date != '') {
                $date_fin = $request->final_date.' 23:59:59';

                $loans = DB::table('view_loan_borrower')
                ->join('movement_fund_rotatories','movement_fund_rotatories.loan_id','=','view_loan_borrower.id_loan')
                ->where('movement_fund_rotatories.created_at',  "<=", $date_fin)
                ->where("view_loan_borrower.state_loan", "Vigente")
                ->where("movement_fund_rotatories.movement_concept_id",'=',$movement_concept->id)
                ->whereNull("movement_fund_rotatories.deleted_at")
                ->select('*')
                ->orderBy('movement_fund_rotatories.id')
                ->get();
            } else {
                $date_fin = Carbon::now();
                if ($initial_date != '') {
                    $date_ini = $request->initial_date.' 00:00:00';

                    $loans = DB::table('view_loan_borrower')
                    ->join('movement_fund_rotatories','movement_fund_rotatories.loan_id','=','view_loan_borrower.id_loan')
                    ->where('movement_fund_rotatories.created_at', ">=", $date_ini)
                    ->where("view_loan_borrower.state_loan", "Vigente")
                    ->where("movement_fund_rotatories.movement_concept_id",'=',$movement_concept->id)
                    ->whereNull("movement_fund_rotatories.deleted_at")
                    ->select('*')
                    ->orderBy('movement_fund_rotatories.id')
                    ->get();
                } else {
                    $loans = DB::table('view_loan_borrower')
                    ->join('movement_fund_rotatories','movement_fund_rotatories.loan_id','=','view_loan_borrower.id_loan')
                    ->where("view_loan_borrower.state_loan", "Vigente")
                    ->where("movement_fund_rotatories.movement_concept_id",'=',$movement_concept->id)
                    ->whereNull("movement_fund_rotatories.deleted_at")
                    ->select('*')
                    ->orderBy('movement_fund_rotatories.id')
                    ->get();
                }
            }
        }
            $loans_array = collect([]);
            foreach ($loans as $loan) {
                $loans_array->push([
                "code" => $loan->movement_concept_code,
                "disbursement_date_loan" => $loan->created_at,
                "code_loan" => $loan->code_loan,
                "full_name_borrower" => $loan->full_name_borrower,
                "amount_approved_loan" => $loan->output_amount,
                ]);
            }
            //return Carbon::parse($loans_array[0]['disbursement_date_loan'])->format('d-m-Y');
            $data = [
            'header' => [
                'direction' => 'DIRECCIÓN DE ASUNTOS ADMINISTRATIVOS',
                'unity' => '',
                'table' => [
                    ['Fecha', Carbon::now()->format('d-m-Y')],
                    ['Hora', Carbon::now()->format('H:i:s')],
                    ['Usuario', Auth::user()->username]
                ]
            ],
            'title' => 'REPORTE DE DESEMBOLSOS',
            'initial_date' => $initial_date? $initial_date: Carbon::parse($loans_array[0]['disbursement_date_loan'])->format('d-m-Y'),
            'final_date' => $final_date,
            'loans' => $loans_array,
            'file_title' => 'reporte de desembolsos',
        ];
            $file_name = 'Reporte salidas fondo rotatorio.pdf';
            $view = view()->make('loan.reports_tesoreria.output_report')->with($data)->render();
            if ($standalone) {
                return Util::pdf_to_treasury_receipt([$view],'letter', $request->copies ?? 1);
            }
            return $view;
    }

}
