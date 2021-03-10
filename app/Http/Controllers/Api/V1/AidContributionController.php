<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AidContribution;
use App\Http\Requests\AidContributionForm;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Util;


/** @group Contribuciones sector pasivo
* Datos de las contribuciones del sector pasivo
*/

class AidContributionController extends Controller
{
    public static function append_data(AidContribution $aid_contribution)
    {
        return $aid_contribution;
    }
    /**
    * Lista de contribuciones de sector Pasivo
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: 2020-02-03
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/aid_contribution/index.200.json
    */
    public function index(Request $request)
    {
        return Util::search_sort(new AidContribution(), $request);
    }

    /**
    * Nuevo registro de contribución para el sector Pasivo
    * Inserta nueva contribucion del afiliado
    * @bodyParam affiliate_id integer required ID del afiliado es requerido. Example: 10528
    * @bodyParam month_year date required mes de la boleta es requerido. Example: 2020-02-01
    * @bodyParam rent numeric required Monto de la renta es requerido. Example: 1255.5
    * @bodyParam dignity_rent numeric required Monto de la renta dignidad es requerido. Example: 200
    * @bodyParam type string Tipo Ejemplo planilla. Example: PLANILLA
    * @bodyParam quotable numeric Es la resta de los campos rent y dignity_rent . Example: 1055.5
    * @bodyParam interest numeric Monto de interes. Example: 10.5
    * @bodyParam total numeric Monto total. Example: 10.5
    * @bodyParam affiliate_contribution boolean Contribución afiliado. Example: false
    * @bodyParam mortuary_aid numeric Monto mortuary_aid. Example: 51
    * @bodyParam valid boolean Valor booleano. Example: false
    * @authenticated
    * @responseFile responses/aid_contribution/store.200.json
   */

    public function store(AidContributionForm $request)
    {
        $aid_contribution = new AidContribution;
        $aid_contribution->user_id = Auth::id();
        $aid_contribution->affiliate_id = $request->affiliate_id;//req
        $aid_contribution->month_year = $request->month_year;//req
        $aid_contribution->rent = $request->input('rent', 0);//req
        $aid_contribution->dignity_rent = $request->input('dignity_rent', 0);//req

        $aid_contribution->type = $request->input('type', 'PLANILLA');//optional

        $aid_contribution->quotable = $request->input('quotable', $request->rent - $request->dignity_rent);//optional
        $aid_contribution->interest = $request->input('interest', 0);//op
        $aid_contribution->total = $request->input('total', 0);//op

        $aid_contribution->affiliate_contribution = $request->input('affiliate_contribution', false);//op
        $aid_contribution->mortuary_aid = $request->input('mortuary_aid', 0);//op
        $aid_contribution->valid = $request->input('valid', true);//op

        return AidContribution::create($aid_contribution->toArray());
    }

    /**
    * Detalle de la contribución
    * Devuelve el detalle de un registro de una contribución mediante su ID
    * @urlParam id_aidContribution required ID de la contribución del sec pasivo. Example: 10528
    * @responseFile responses/aid_contribution/show.200.json
    * @response
    */
    public function show($aid_contribution)
    {
        $aid_contribution = AidContribution::find($aid_contribution);
        return $aid_contribution;
    }

    /**
    * Actualizar contribucion del sector pasivo
    * Actualizar datos principales destino de préstamo
    * @urlParam aid_contribution ID de destino de Préstamo. Example: 591292
    * @bodyParam affiliate_id integer ID del afiliado es requerido. Example: 10528
    * @bodyParam month_year date mes de la boleta es requerido. Example: 2020-02-01
    * @bodyParam rent numeric  Monto de la renta es requerido. Example: 1255.5
    * @bodyParam dignity_rent numeric  Monto de la renta dignidad es requerido. Example: 300
    * @bodyParam type string Tipo Ejemplo planilla. Example: PLANILLA
    * @bodyParam quotable numeric Es la resta de los campos rent y dignity_rent . Example: 1055.5
    * @bodyParam interest numeric Monto de interes. Example: 10.5
    * @bodyParam total numeric Monto total. Example: 10.5
    * @bodyParam affiliate_contribution boolean Contribución afiliado. Example: false
    * @bodyParam mortuary_aid numeric Monto mortuary_aid. Example: 51
    * @bodyParam valid boolean Valor booleano. Example: false
    * @authenticated
    * @responseFile responses/aid_contribution/update.200.json
    */
    public function update(AidContributionForm $request,$aidContribution)
    { 
        $aidContribution = AidContribution::find($aidContribution);

        $aidContribution->fill($request->all());
        $aidContribution->save();
        $aidContribution->update([
            'quotable' => $aidContribution->rent-$aidContribution->dignity_rent
        ]);
        return  $aidContribution;
    }

    /**
    * Eliminar contribucion del sector pasivo
    * @urlParam aid_contribution required ID de la contribucion del afiliado sec pasivo. Example: 5
    * @authenticated
    * @responseFile responses/aid_contribution/destroy.200.json
    */
    public function destroy($aid_contribution)
    {
        $aid_contribution = AidContribution::find($aid_contribution);
        if (!$aid_contribution) abort(409, 'No existe la contribución');
        $aid_contribution->delete();
        return $aid_contribution;
    }
    /**
    * Actualizar o crear contribucion
    * @bodyParam affiliate_id integer required ID del afiliado es requerido. Example: 10528
    * @bodyParam month_year date required mes de la boleta es requerido. Example: 2020-02-01
    * @bodyParam rent numeric required Monto de la renta es requerido. Example: 1255.5
    * @bodyParam dignity_rent numeric required Monto de la renta dignidad es requerido. Example: 200
    * @bodyParam type string Tipo Ejemplo planilla. Example: PLANILLA
    * @bodyParam quotable numeric Es la resta de los campos rent y dignity_rent . Example: 1055.5
    * @bodyParam interest numeric Monto de interes. Example: 10.5
    * @bodyParam total numeric Monto total. Example: 10.5
    * @bodyParam affiliate_contribution boolean Contribución afiliado. Example: false
    * @bodyParam mortuary_aid numeric Monto mortuary_aid. Example: 51
    * @bodyParam valid boolean Valor booleano. Example: false
    * @authenticated
    * @responseFile responses/aid_contribution/updateOrCreate.200.json
    */
    public function updateOrCreate(AidContributionForm $request)
    {
        $aid_contribution = AidContribution::where('month_year',$request->month_year)->where('affiliate_id',$request->affiliate_id)->first();
        if($aid_contribution){
            $aid_contribution->fill($request->all());
            $aid_contribution->save();
            $aid_contribution->update([
                'quotable' => $aid_contribution->rent-$aid_contribution->dignity_rent
            ]);
        }else{
            $aid_contribution = $this->store($request);
        }
        return $aid_contribution;
    }
}
