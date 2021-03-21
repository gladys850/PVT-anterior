<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Util;
use App\User;
use App\LoanContributionAdjust;
use App\Http\Requests\LoanContributionAdjustForm;
use Illuminate\Support\Facades\Auth;
use App\Events\FingerprintSavedEvent;
/** @group Ajuste a las Contribución
* Datos del registro de del ajuste a las contrubicion de un préstamos
*/
class LoanContributionAdjustController extends Controller
{
    /**
    * Lista del Ajuste a Contribución 
    * Devuelve el listado con los datos paginados
    * @bodyParam user_id integer ID de usuario. Example: 1
    * @bodyParam loan_id integer ID de prestamo. Example: 1
    * @bodyParam affiliate_id  integer required ID de afiliado. Example: 5
    * @bodyParam adjustable_id  integer required ID del registro de la tabla contributions,aid_contributions,affiliates.Example: 1
    * @bodyParam adjustable_type string required registro del nombre de las tablas tabla contributions,aid_contributions ,affiliates. Example: contributions
    * @bodyParam type_affiliate  enum  required tipificación del afiliado como (lender,guarantor,cosigner) Example: lender
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum required   (adjust,liquid) Example: adjust
    * @bodyParam period_date fecha required  Periodo a la que corresponde la boleta Example:2010-06-20
    * @bodyParam description string required  Descripcion del por que se realizo el ajuste del liquido. Example:ninguno
    * @queryParam search Parámetro de búsqueda. Example: 2000
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/loan_contribution_adjust/index.200.json
     */
    
    public function index(Request $request)
    {
        return Util::search_sort(new LoanContributionAdjust(), $request);
    }
     /**
    * Nueva ajuste a Contribución
    * Inserta nueva ajuste de Contribución
    * @bodyParam loan_id integer ID de prestamo. Example: 5
    * @bodyParam affiliate_id  integer required ID de afiliado. Example: 5
    * @bodyParam adjustable_id  integer required ID del registro de la tabla contributions,aid_contributions,affiliates.Example: 1
    * @bodyParam adjustable_type string required registro del nombre de las tablas tabla contributions,aid_contributions ,affiliates. Example: contributions
    * @bodyParam type_affiliate  enum  required tipificación del afiliado como (lender,guarantor,cosigner) Example: lender
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum required   (adjust,liquid) Example: adjust
    * @bodyParam period_date fecha required  Periodo a la que corresponde la boleta Example:2010-06-20
    * @bodyParam description string required  Descripcion del por que se realizo el ajuste del liquido. Example:ninguno
    * @authenticated
    * @responseFile responses/loan_contribution_adjust/store.200.json
    */
    public function store(LoanContributionAdjustForm $request)
    {   
        $request=$request->all();
        $request['user_id']=Auth::id();
        return LoanContributionAdjust::create($request);
    }
    /**
    * Detalle del ajuste a una contribución
    * Devuelve el detalle de un registro del ajuste de una contribución mediante su ID
    * @urlParam $id required ID de la LoanContributionAdjust . Example: 10528
    * @responseFile responses/loan_contribution_adjust/show.200.json
    * @response
    */
    public function show($id)
    {
        $loan_contribution = LoanContributionAdjust::find($id);
        return $loan_contribution;
    }
     /**
    * Actualizar ajuste a Contribución
    * Actualizar datos ajuste de Contribución
    * @urlParam rquired ID de ajuste
    * @bodyParam affiliate_id integer ID de afiliado. Example: 5
    * @bodyParam adjustable_id  integer required ID del registro de la tabla contributions,aid_contributions,affiliates.Example: 1
    * @bodyParam adjustable_type string required registro del nombre de las tablas tabla contributions,aid_contributions ,affiliates. Example: contributions
    * @bodyParam type_affiliate enum (lender,guarantor,cosigner) Example:cosigner
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum (adjust,liquid)  Example: 10000.50
    * @bodyParam period_date fecha Periodo a la que corresponde la boleta Example: 2010-06-20 
    * @bodyParam description string Descripcion del por que se realizo el ajuste del liquido. Example:ninguno
    * @authenticated
    * @responseFile responses/loan_contribution_adjust/update.200.json
    */
    
   
    public function update(LoanContributionAdjustForm $request, LoanContributionAdjust $LoanContributionAdjust)
    { 
        $LoanContributionAdjust->fill($request->all());
        $LoanContributionAdjust->save();
        return  $LoanContributionAdjust;
    }

    /**
    * Eliminar un registro de la tabla loan_contribution_adjust
    * @urlParam loan_contribution_adjust required ID del registro del Ajuste a la construcción . Example: 5
    * @authenticated
    * @responseFile responses/loan_contribution_adjust/destroy.200.json
    */
    public function destroy(LoanContributionAdjust $LoanContributionAdjust)
    {
        $LoanContributionAdjust->delete();
        return $LoanContributionAdjust;
    }
     /**
    * Actualizar o crear el ajuste a las contribuciones
    * Actualizar datos ajuste de Contribución
    * @urlParam rquired ID de ajuste
    * @bodyParam affiliate_id integer ID de afiliado. Example: 5
    * @bodyParam adjustable_id  integer required ID del registro de la tabla contributions,aid_contributions,affiliates.Example: 1
    * @bodyParam adjustable_type string required registro del nombre de las tablas tabla contributions,aid_contributions ,affiliates. Example: contributions
    * @bodyParam type_affiliate enum (lender,guarantor,cosigner) Example:cosigner
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum (adjust,liquid)  Example: 10000.50
    * @bodyParam period_date fecha Periodo a la que corresponde la boleta Example: 2010-06-20 
    * @bodyParam description string Descripcion del por que se realizo el ajuste del liquido. Example:ninguno
    * @authenticated
    * @responseFile responses/loan_contribution_adjust/updateOrCreate.200.json
    */
    public function updateOrCreate(LoanContributionAdjustForm $request)
    {
        $adjust_contribution = LoanContributionAdjust::where('affiliate_id',$request->affiliate_id)
                                                  ->where('adjustable_type',$request->adjustable_type)
                                                  ->where('adjustable_id',$request->adjustable_id)
                                                  ->where('type_affiliate',$request->type_affiliate)
                                                  ->where('type_adjust',$request->type_adjust)
                                                  ->where('period_date',$request->period_date)->first();
        if($adjust_contribution){
            $adjust_contribution->fill($request->all());
            $adjust_contribution->save();
        }else{
            $adjust_contribution = $this->store($request);
        }
        return $adjust_contribution;
    }
}
