<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Spouse;
use App\Http\Requests\SpouseForm;
use Illuminate\Http\Request;
use Util;

/** @group Cónyugues
* Datos de los cónyugues y métodos para obtener y establecer sus relaciones
*/
class SpouseController extends Controller
{
    /**
    * Lista de cónyugues
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: TORRE
    * @queryParam sortBy Vector de ordenamiento. Example: [last_name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [false]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *     "current_page": 1,
    *     "data": [
    *         {
    *             "id": 21,
    *             "user_id": 1,
    *             "affiliate_id": 51992,
    *             "city_identity_card_id": 1,
    *             "identity_card": "1700723",
    *             "registration": "0",
    *             "last_name": "TEMO",
    *             "mothers_last_name": "JOU",
    *             "first_name": "MANUELA",
    *             "second_name": null,
    *             "surname_husband": "VDA. DE URGEL",
    *             "civil_status": "V",
    *             "birth_date": "1934-03-29",
    *             "date_death": null,
    *             "reason_death": null,
    *             "created_at": "2017-06-08 11:56:17",
    *             "updated_at": "2017-06-08 11:56:17",
    *             "deleted_at": null,
    *             "city_birth_id": null,
    *             "death_certificate_number": null,
    *             "due_date": null,
    *             "is_duedate_undefined": false,
    *             "official": null,
    *             "book": null,
    *             "departure": null,
    *             "marriage_date": null
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/spouse?page=1",
    *     "from": 1,
    *     "last_page": 244,
    *     "last_page_url": "http://127.0.0.1/api/v1/spouse?page=244",
    *     "next_page_url": "http://127.0.0.1/api/v1/spouse?page=2",
    *     "path": "http://127.0.0.1/api/v1/spouse",
    *     "per_page": 10,
    *     "prev_page_url": null,
    *     "to": 10,
    *     "total": 2439
    * }
    */
    public function index(Request $request)
    {
        return Util::search_sort(new Spouse(), $request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
    * Nuevo cónyugue
    * Inserta nuevo cónyugue
    * @bodyParam first_name string required Primer nombre. Example: MAXIMA
    * @bodyParam last_name string required Apellido paterno. Example: ARUQUIPA
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 4
    * @bodyParam birth_date date required Fecha de nacimiento (AÑO-MES-DÍA). Example: 1980-05-02
    * @bodyParam city_birth_id integer required ID de ciudad de nacimiento. Example: 10
    * @bodyParam affiliate_id integer required ID del afiliado. Example: 536
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam civil_status string required Estado civil (S,C,D,V). Example: C
    * @bodyParam second_name string Segundo nombre. Example: ELOISA
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAS
    * @bodyParam due_date date Fecha de vencimiento del CI. Example: 2018-01-05
    * @bodyParam marriage_date date Fecha de matrimonio (AÑO-MES-DÍA). Example: 2001-04-30
    * @bodyParam registration string Matrícula. Example: 870914VBW
    * @bodyParam surname_husband string Apellido de casada. Example: PAREDES
    * @bodyParam date_death date Fecha de fallecimiento. Example: 2018-09-21
    * @bodyParam reason_death string Causa de fallecimiento. Example: Embolia
    * @bodyParam death_certificate_number string Número de certificado de defunción. Example: 180923-ATR
    * @authenticated
    * @response
    * {
    *     "id": 1301,
    *     "affiliate_id": 3065,
    *     "city_identity_card_id": 4,
    *     "identity_card": "460748",
    *     "registration": "870914VBW",
    *     "last_name": "ARUQUIPA",
    *     "mothers_last_name": "TAPIA",
    *     "first_name": "MAXIMA",
    *     "second_name": "ELOISA",
    *     "surname_husband": "PAREDES",
    *     "civil_status": "C",
    *     "birth_date": "1950-04-08",
    *     "date_death": "2017-10-29",
    *     "reason_death": "DIABETES TIPO 2 DESCOMPENSADA",
    *     "created_at": "2018-08-23 11:38:28",
    *     "updated_at": "2018-11-15 10:55:08",
    *     "deleted_at": null,
    *     "city_birth_id": 4,
    *     "death_certificate_number": "180923-ATR",
    *     "due_date": "2018-01-05",
    *     "is_duedate_undefined": false,
    *     "official": "600",
    *     "book": "0024-64",
    *     "departure": "143",
    *     "marriage_date": "1964-12-12"
    * }
    */
    public function store(SpouseForm $request)
    {
        return Spouse::create($request->all());
    }

    /**
    * Detalle de cónyugue
    * Devuelve el detalle de un cónyugue mediante su ID
    * @urlParam id required ID de cónyugue. Example: 42
    * @authenticated
    * @response
    * {
    *     "id": 42,
    *     "user_id": 47,
    *     "affiliate_id": 12,
    *     "city_identity_card_id": 2,
    *     "identity_card": "1048652",
    *     "registration": "",
    *     "last_name": "FORTUN",
    *     "mothers_last_name": null,
    *     "first_name": "MARIA",
    *     "second_name": "LUISA",
    *     "surname_husband": "VDA. DE VILLALBA",
    *     "civil_status": "V",
    *     "birth_date": "1947-06-08",
    *     "date_death": null,
    *     "reason_death": "",
    *     "created_at": "2017-06-08 11:56:17",
    *     "updated_at": "2019-06-14 11:25:21",
    *     "deleted_at": null,
    *     "city_birth_id": 2,
    *     "death_certificate_number": "",
    *     "due_date": null,
    *     "is_duedate_undefined": true,
    *     "official": "600",
    *     "book": "0024-64",
    *     "departure": "143",
    *     "marriage_date": "1964-12-12"
    * }
    */
    public function show($id)
    {
        $spouse = Spouse::findOrFail($id);
        $this->append_data($spouse);
        return $spouse;
    }

    /**
    * Actualizar cónyugue
    * Actualizar datos personales de cónyugue
    * @urlParam spouse required ID de cónyugue. Example: 42
    * @bodyParam first_name string required Primer nombre. Example: MAXIMA
    * @bodyParam last_name string required Apellido paterno. Example: ARUQUIPA
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 4
    * @bodyParam birth_date date required Fecha de nacimiento (AÑO-MES-DÍA). Example: 1980-05-02
    * @bodyParam city_birth_id integer required ID de ciudad de nacimiento. Example: 10
    * @bodyParam affiliate_id integer required ID del afiliado. Example: 536
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam civil_status string required Estado civil (S,C,D,V). Example: C
    * @bodyParam second_name string Segundo nombre. Example: ELOISA
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAS
    * @bodyParam due_date date Fecha de vencimiento del CI. Example: 2018-01-05
    * @bodyParam marriage_date date Fecha de matrimonio (AÑO-MES-DÍA). Example: 2001-04-30
    * @bodyParam registration string Matrícula. Example: 870914VBW
    * @bodyParam surname_husband string Apellido de casada. Example: PAREDES
    * @bodyParam date_death date Fecha de fallecimiento. Example: 2018-09-21
    * @bodyParam reason_death string Causa de fallecimiento. Example: Embolia
    * @bodyParam death_certificate_number string Número de certificado de defunción. Example: 180923-ATR
    * @authenticated
    * @response
    * {
    *     "id": 42,
    *     "user_id": 47,
    *     "affiliate_id": 12,
    *     "city_identity_card_id": 2,
    *     "identity_card": "1048652",
    *     "registration": "",
    *     "last_name": "FORTUN",
    *     "mothers_last_name": null,
    *     "first_name": "MARIA",
    *     "second_name": "LUISA",
    *     "surname_husband": "VDA. DE VILLALBA",
    *     "civil_status": "V",
    *     "birth_date": "1947-06-08",
    *     "date_death": null,
    *     "reason_death": "",
    *     "created_at": "2017-06-08 11:56:17",
    *     "updated_at": "2019-06-14 11:25:21",
    *     "deleted_at": null,
    *     "city_birth_id": 2,
    *     "death_certificate_number": "",
    *     "due_date": null,
    *     "is_duedate_undefined": true,
    *     "official": "600",
    *     "book": "0024-64",
    *     "departure": "143",
    *     "marriage_date": "1964-12-12"
    * }
    */
    public function update(SpouseForm $request, $id)
    {
        $spouse = Spouse::findOrFail($id);
        $spouse->fill($request->all());
        $spouse->save();
        return $spouse;
    }

    /**
    * Eliminar cónyugue
    * Eliminar un cónyugue solo en caso de no estar relacionado a ningún afiliado
    * @urlParam id required ID de cónyugue. Example: 42
    * @authenticated
    * @response
    * {
    *     "id": 42,
    *     "user_id": 47,
    *     "affiliate_id": 12,
    *     "city_identity_card_id": 2,
    *     "identity_card": "1048652",
    *     "registration": "",
    *     "last_name": "FORTUN",
    *     "mothers_last_name": null,
    *     "first_name": "MARIA",
    *     "second_name": "LUISA",
    *     "surname_husband": "VDA. DE VILLALBA",
    *     "civil_status": "V",
    *     "birth_date": "1947-06-08",
    *     "date_death": null,
    *     "reason_death": "",
    *     "created_at": "2017-06-08 11:56:17",
    *     "updated_at": "2019-06-14 11:25:21",
    *     "deleted_at": null,
    *     "city_birth_id": 2,
    *     "death_certificate_number": "",
    *     "due_date": null,
    *     "is_duedate_undefined": true,
    *     "official": "600",
    *     "book": "0024-64",
    *     "departure": "143",
    *     "marriage_date": "1964-12-12"
    * }
    */
    public function destroy($id)
    {
        $spouse = Spouse::findOrFail($id);
        $spouse->delete();
        return $spouse;
    }
}
