<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Util;
use App\User;
use App\Affiliate;
use App\MovementConcept;
use App\Http\Requests\MovementConceptForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
/** @group Conceptos de movimientos
* Conceptos de movimientos de dinero
*/
class MovementConceptController extends Controller
{
    /**
    * Listar los Conceptos de movimiento
    * Devuelve el listado de los conceptos de movimiento
    * @queryParam search Parámetro de búsqueda. Example: 2020
    * @queryParam sortBy Vector de ordenamiento. Example: []
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/movement_concept/index.200.json
     */
    public function index(Request $request)
    {
        return Util::search_sort(new MovementConcept(), $request);
    }

    /**
     * Nuevo registro Conceptos de movimiento
     * Inserta nuevo Conceptos de movimiento
     * @bodyParam name string required Nombre del concepto. Example: DESEMBOLSO ANTICIPO
     * @bodyParam shortened string required Nombre corto del concepto. Example: DES-ANT
     * @bodyParam description string required Descripcion del concepto. Example: Desembolos de prestamos anticipo
     * @bodyParam type string required Tipo de concepto enum "EGRESO" O "INGRESO". Example: EGRESO
     * @bodyParam abbreviated_supporting_document string Abreviatura del documento de apoyo de concepto. Example: REC
     * @authenticated
     * @responseFile responses/movement_concept/store.200.json
     */
    public function store(MovementConceptForm $request)
    {
        DB::beginTransaction();
        try {
            $request=$request->all();
            $request['user_id']=Auth::id();
            $movementConcept = MovementConcept::create($request);
            DB::commit();
            return $movementConcept;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Detalle del Conceptos de movimiento
     * Devuelve el detalle de un Conceptos de movimiento
     * @urlParam id required ID del Concepto. Example: 1
     * @responseFile responses/movement_concept/show.200.json
     * @response
     */
    public function show($id)
    {
        $movementConcept = MovementConcept::find($id);
        return $movementConcept;
    }

    /**
     * Actualizar informacion del Conceptos de movimiento
     * Actualizar datos del Conceptos de movimiento
     * @urlParam movement_concept_id ID del fondo rotatorio. Example: 1
     * @bodyParam name string required Nombre del concepto. Example: DESEMBOLSO ANTICIPO
     * @bodyParam shortened string required Nombre corto del concepto. Example: DES-ANT
     * @bodyParam description string required Descripcion del concepto. Example: Desembolos de prestamos anticipo
     * @bodyParam type string required Tipo de concepto enum "EGRESO" O "INGRESO". Example: EGRESO
     * @bodyParam abbreviated_supporting_document string Abreviatura del documento de apoyo de concepto. Example: REC
     * @authenticated
     * @responseFile responses/movement_concept/update.200.json
     */
    public function update(MovementConceptForm $request, MovementConcept $movementConcept)
    { 
        $movementConcept->fill($request->all());
        $movementConcept->save();
        return  $movementConcept;
    }

    /**
     * Eliminar Conceptos de movimiento
     * Eliminar datos del Conceptos de movimiento
     * @urlParam movement_concept_id ID del fondo rotatorio. Example: 1
     * @authenticated
     * @responseFile responses/movement_concept/destroy.200.json
     */
    public function destroy($fundRotatory)
    {
        $fundRotatory = MovementConcept::find($fundRotatory);
        $fundRotatory->delete();
        return $fundRotatory;
    }
}
