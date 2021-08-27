<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MovementFundRotatory;
use App\Http\Requests\MovementFundRotatoryForm;

use App\Affiliate;
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
}
