<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Util;
use App\LoanContributionAdjust;

class LoanContributionAdjustController extends Controller
{
    /**
    * Lista del ajuste a contruciones que se realizado
    * Devuelve el listado con los datos paginados
    * @bodyParam user_id integer ID de usuario. Example: 1
    * @bodyParam loan_id integer ID de prestamo. Example: 1
    * @bodyParam affiliate_id integer ID de afiliado. Example: 5
    * @bodyParam adjustable_id integer ID del registro de la tabla contribution,aid_contribution. Example: 1
    * @bodyParam adjustable_type string registro del modelo de la tabla contribution,aid_contribution . Example: contribution
    * @bodyParam type_affiliate enum tipificación del afiliado como (headline,guarantor,cosigner)
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum (adjust,liquid)
    * @bodyParam period_date fecha Periodo a la que corresponde la boleta Example: 20-06-10. 
    * @bodyParam description string Descripcion del por que se realizo el ajuste del liquido. Example: 
    * @queryParam search Parámetro de búsqueda. Example: 2000
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/Loan_contribution_adjust/index.200.json
     */
    
    public function index(Request $request)
    {
        return Util::search_sort(new LoanContributionAdjust(), $request);
    }
     /**
    * Nueva ajuste a contruciones
    * Inserta nueva ajuste de construcción
    * @bodyParam user_id integer ID de usuario. Example: 5
    * @bodyParam loan_id integer ID de prestamo. Example: 5
    * @bodyParam affiliate_id integer ID de afiliado. Example: 5
    * @bodyParam adjustable_id integer ID del registro de la tabla contribution,aid_contribution. Example: 1
    * @bodyParam adjustable_type string registro del modelo de la tabla contribution,aid_contribution . Example: contribution
    * @bodyParam type_affiliate enum tipificación del afiliado como (headline,guarantor,cosigner)
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum (adjust,liquid)
    * @bodyParam period_date fecha Periodo a la que corresponde la boleta Example: 20-06-10. 
    * @bodyParam description string Descripcion del por que se realizo el ajuste del liquido. Example: 
    * @authenticated
    * @responseFile responses/Loan_contribution_adjust/store.200.json
    */
    public function store(Request $request)
    {
        return LoanContributionAdjust::create($request->all());
    }
     /**
    * Nueva ajuste a contruciones
    * Actualizar datos ajuste de construcción
    * @urlParam rquired ID de ajuste
    * @bodyParam user_id integer ID de usuario. Example: 5
    * @bodyParam loan_id integer ID de prestamo. Example: 5
    * @bodyParam affiliate_id integer ID de afiliado. Example: 5
    * @bodyParam adjustable_id integer ID del registro de la tabla contribution,aid_contribution. Example: 1
    * @bodyParam adjustable_type string registro del modelo de la tabla contribution,aid_contribution . Example: contribution
    * @bodyParam type_affiliate enum (headline,guarantor,cosigner)
    * @bodyParam amount numeric de ajuste para el liquido Example: 10000.50
    * @bodyParam type_adjust enum (adjust,liquid)
    * @bodyParam period_date fecha Periodo a la que corresponde la boleta Example: 20-06-10. 
    * @bodyParam description string Descripcion del por que se realizo el ajuste del liquido. Example: 
    * @authenticated
    * @responseFile responses/Loan_contribution_adjust/update.200.json
    */
   
    public function update(Request $request, LoanContributionAdjust $LoanContributionAdjust )
    {
        $LoanContributionAdjust->fill($request->all());
        $LoanContributionAdjust->save();
        return  $LoanContributionAdjust;
    }

    /**
    * Eliminar un registro de la tabla loan_contribution_adjust
    * @urlParam Loan_contribution_adjust required ID del registro del Ajuste a la construcción . Example: 5
    * @authenticated
    * @responseFile responses/Loan_contribution_adjust/destroy.200.json
    */
    public function destroy(LoanContributionAdjust $LoanContributionAdjust)
    {
        $LoanContributionAdjust->delete();
        return $LoanContributionAdjust;
    }
}
