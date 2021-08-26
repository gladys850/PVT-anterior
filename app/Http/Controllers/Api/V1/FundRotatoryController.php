<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Util;
use App\User;
use App\Affiliate;
use App\FundRotatory;
use App\FundRotatoryOutput;
use App\Http\Requests\FundRotatoryForm;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\V1\LoanController;
use Illuminate\Support\Facades\DB;

/** @group Fondo Rotatorio Anticipos
* Fondo rotatorio para anticipos
*/

class FundRotatoryController extends Controller
{
    /**
    * Listar los Fondos rotatorios
    * Devuelve el listado de los fondos rotatorios de anticipos
    * @queryParam search Parámetro de búsqueda. Example: 2020
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/fund_rotary_entry/index.200.json
     */
    public function index(Request $request)
    {
        return Util::search_sort(new FundRotatory(), $request);
    }

    /**
     * Nuevo registro de fondo Rotatorio
     * Inserta nuevo fondo rotatorio
     * @bodyParam check_number numeric required Numero del Cheque del fondo rotatorio. Example: 112
     * @bodyParam amount numeric required Monto de ingreso del fondo rotatoio. Example: 50000
     * @bodyParam date_check_delivery date required Fecha de ingreso del fondo o asignacion. Example: 2021-06-01
     * @bodyParam role_id numeric required Rol con el cual se realizo el registro. Example: 92
     * @bodyParam description string  Descripcion del registro de del fondo Rotatorio. Example: Ingres
     * @authenticated
     * @responseFile responses/fund_rotary_entry/store.200.json
     */
    public function store(FundRotatoryForm $request)
    {
        DB::beginTransaction();
        try {
            $fundRotatory = new FundRotatory;
            $fundRotatory->user_id = Auth::id();
            $code_entry = count(FundRotatory::all());
            $code_entry = $code_entry+1;
            $fundRotatory->code_entry ="FR-".$code_entry;
            $fundRotatory->check_number = $request->input('check_number');
            $fundRotatory->amount = $request->input('amount');
            $fundRotatory->date_check_delivery = $request->input('date_check_delivery');
            $fundRotatory->description = $request->input('description');
            $fundRotatory->role_id = $request->input('role_id');
            if($fundRotatory->last == null){
                $fundRotatory->balance_previous= 0;
                $fundRotatory->balance = $request->input('amount');
            }else{
                $balance_previous= $fundRotatory->last->balance;
                $fundRotatory->balance_previous = $balance_previous;
                $fundRotatory->balance = $request->input('amount')+$fundRotatory->last->balance;
            }
            $fundRotatory_return = FundRotatory::create($fundRotatory->toArray());
            DB::commit();
            return $fundRotatory_return;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Detalle del fondo rotatorio
     * Devuelve el detalle de un fondo rotatorio
     * @urlParam id required ID del Fondo Rotatorio. Example: 1
     * @responseFile responses/fund_rotary_entry/show.200.json
     * @response
     */
    public function show($id)
    {
        $fundRotatory = FundRotatory::find($id);
        return $fundRotatory;
    }

    /**
     * Actualizar informacion del fondo rotatorio
     * Actualizar datos del fondo rotatorio
     * @urlParam fund_rotatory_id ID del fondo rotatorio. Example: 1
     * @bodyParam amount numeric Monto de ingreso del fondo rotatoio. Example: 50000
     * @bodyParam date_check_delivery date Fecha de ingreso del fondo o asignacion. Example: 2021/06/01
     * @bodyParam check_number string Cheque numero. Example: CH-12589
     * @bodyParam description string Descripcion del cheque: Primer cheque
     * @bodyParam role_id numeric Rol con el cual se realizo el registro. Example: 92
     * @authenticated
     * @responseFile responses/fund_rotary_entry/update.200.json
     */
    public function update(FundRotatoryForm $request,$fundRotatory)
    {
        DB::beginTransaction();
        try {
            $message = 'Envíe lo datos que desee actualizar.';
            $fundRotatory = FundRotatory::find($fundRotatory);
            $verify_fund_rotatory_disbursements = $fundRotatory->verify_fund_rotatory_disbursements();
            if($fundRotatory->verify_fund_rotatory_disbursements()){ //tiene salidas el FR
                $exceptions = ['code_entry','date_check_delivery','role_id','amount','balance_previous','balance','user_id'];
                if($request->has('amount')) $message = 'No se puede modificar MONTO del fondo rotatorio, por que existen salidas registradas';
                if($request->has('date_check_delivery')) $message = 'No se puede modificar Fecha de entrega del Cheque del fondo rotatorio, por que existen salidas registradas';
                if($request->has('date_check_delivery') && $request->has('amount')) $message = 'No se puede modificar Monto y Fecha de entrega del Cheque del fondo rotatorio, por que existen salidas registradas';
            }else{
                $exceptions = ['code_entry','balance_previous','balance'];
            }
            if($request->has('amount') && !$fundRotatory->verify_fund_rotatory_disbursements()){
                $fundRotatory->fill(array_merge($request->except($exceptions),['balance' =>  $fundRotatory->balance_previous + $request->amount]));
                if($fundRotatory->isDirty()) $message = 'Datos actualizados correctamente.';
                $updated_data =$fundRotatory->getDirty();
            }else{
                $fundRotatory->fill(array_merge($request->except($exceptions)));
                if($fundRotatory->isDirty()) $message = 'Datos actualizados correctamente.';
                $updated_data =$fundRotatory->getDirty();
            }

            $fundRotatory->save();
            DB::commit();
            return response()->json([
                'fundRotatory' =>  $fundRotatory,
                'message' => $message,
                'updated_data' => $updated_data
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Eliminar fondo rotatorio
     * Eliminar datos del fondo rotatorio
     * @urlParam fund_rotatory_id ID del fondo rotatorio. Example: 1
     * @authenticated
     * @responseFile responses/fund_rotary_entry/destroy.200.json
     */
    public function destroy($fundRotatory)
    {
        $fundRotatory = FundRotatory::find($fundRotatory);
        if(!$fundRotatory->verify_fund_rotatory_disbursements()){
            $fundRotatory->delete();
            return $fundRotatory;
        }else{
            abort(409, 'No se puede eliminar, el fondo rotatorio tiene resgistrado salidas! ');
        }
    }
     /**
    * Listado de ingresos y salidas del fondo rotatorio
    * Devuelve un listado de fondo rotatorio y sus salidas
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/fund_rotary_entry/get_fund_rotatori_entry_output.200.json
    */
    public function get_fund_rotatori_entry_output(Request $request)
    {  
        $pagination_rows = request('per_page') ?? 10;
        $fundRotatories =  FundRotatory::orderBy('id')->paginate($pagination_rows);    
        foreach($fundRotatories as $fundRotatory){
                $fundRotatory->amount_egress =$fundRotatory->egress;
                $fundRotatory->fund_rotatory_outputs->sortBy('id');
            foreach($fundRotatory->fund_rotatory_outputs as $loan_outputs){ 
                $loan = $loan_outputs->loan;
                $affiliate = Affiliate::findOrFail($loan->affiliate_id);
                $loan->affiliate = LoanController::verify_loan_affiliates($affiliate,$loan)->disbursable;
                $loan->procedure_type = $loan->modality->procedure_type;
            } 
        } 
        return $fundRotatories;
    }
     /**
    * Verificar fondo rotatorio desmbolsos
    * Verificar si el fondo rotatorio tiene desembolsos asociados debuelve un booleano
    * @queryParam id_fund_rotatory required ID del fondo rotatorio a varificar. Example: 2
    * @authenticated
    * @responseFile responses/fund_rotary_entry/verify_fund_rotatory_disbursements.200.json
    */
    public function verify_fund_rotatory_disbursements(Request $request)
    {
        $request->validate([
            'id_fund_rotatory'=>'required|exists:fund_rotatories,id',
        ]);
        $fundRotatory =  FundRotatory::find($request->id_fund_rotatory);
        return response()->json([
            'has_disbursements' =>  $fundRotatory->verify_fund_rotatory_disbursements()
        ]);
    }
}
