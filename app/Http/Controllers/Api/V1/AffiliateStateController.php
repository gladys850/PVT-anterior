<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AffiliateState;

/** @group Estado de Afiliado
* Datos de los estados de afiliado disponibles en el sistema
*/
class AffiliateStateController extends Controller
{
    /**
    * Lista de estados
    * Devuelve el listado de los posibles estados de afiliado
    * @response
    * [
    *     {
    *         "id": 1,
    *         "affiliate_state_type_id": 1,
    *         "name": "Servicio"
    *     }, {}
    * ]
     */
    public function index()
    {
        return AffiliateState::get();
    }
}
