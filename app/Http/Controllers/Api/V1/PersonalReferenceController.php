<?php

namespace App\Http\Controllers\Api\V1;
use App\PersonalReference;
use App\Http\Requests\PersonalReferenceForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Util;

/** @group Personas referencia préstamos
* Datos de las personas de referencia para trámites de préstamos
*/
class PersonalReferenceController extends Controller
{
    /**
    * Lista de Personas de referencia
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: MARIA
    * @queryParam sortBy Vector de ordenamiento. Example: [last_name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [0]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @response
    * {
    *    "current_page": 1,
    *    "data": [
    *        {
    *            "id": 2,
    *            "city_identity_card_id": 5,
    *            "identity_card": "6857219",
    *            "last_name": "MARIA",
    *            "mothers_last_name": "PEDRINE",
    *            "first_name": "MALDONADO",
    *            "second_name": null,
    *            "surname_husband": null,
    *            "birth_date": "1990-07-27",
    *            "gender": "F",
    *            "civil_status": "C",
    *            "phone_number": "541535",
    *            "cell_phone_number": "541535",
    *            "created_at": "2020-03-03 17:28:25",
    *            "updated_at": "2020-03-03 17:28:25"
    *        }
    *    ],
    *    "first_page_url": "http://192.168.2.242/api/v1/personal_reference?page=1",
    *    "from": 1,
    *    "last_page": 1,
    *    "last_page_url": "http://192.168.2.242/api/v1/personal_reference?page=1",
    *    "next_page_url": null,
    *    "path": "http://192.168.2.242/api/v1/personal_reference",
    *    "per_page": 10,
    *    "prev_page_url": null,
    *    "to": 1,
    *    "total": 1
    * }
    */
    public function index(Request $request)
    {
        return Util::search_sort(new PersonalReference(), $request);
    }

    /**
    * Nueva Persona de Referencia
    * Inserta nueva persona de referencia
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 5
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam last_name string required Apellido paterno. Example: PINTO
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAS
    * @bodyParam first_name string required Primer nombre. Example: JUAN
    * @bodyParam second_name string Segundo nombre. Example: ROBERTO
    * @bodyParam birth_date date required Fecha de nacimiento (AÑO-MES-DÍA). Example: 1980-05-02
    * @bodyParam gender string required Género (M,F). Example: M
    * @bodyParam civil_status string required Estado civil (S,C,D,V). Example: C
    * @bodyParam phone_number integer Número de teléfono fijo. Example: 2254101
    * @bodyParam cell_phone_number integer Número de celular. Example: 76543210
    * @authenticated
    * @response
    * {
    *   "city_identity_card_id": "5",
    *   "identity_card": "165134-1R",
    *   "last_name": "PINTO",
    *   "mothers_last_name": "ROJAS",
    *   "first_name": "JUAN",
    *   "second_name": "ROBERTO",
    *   "birth_date": "1980-05-02",
    *   "gender": "M",
    *   "civil_status": "C",
    *   "phone_number": 2254101,
    *   "cell_phone_number": 76543210,
    *   "updated_at": "2020-03-03 18:16:43",
    *   "created_at": "2020-03-03 18:16:43",
    *   "id": 5 
    * }
    */
    public function store(PersonalReferenceForm $request)
    {
        return PersonalReference::create($request->all());
    }

    /**
    * Detalle de Referencias Personales
    * Devuelve el detalle de una Referencia Personal mediante su ID
    * @urlParam personal_reference required ID de Referencia Personal. Example: 5
    * @response
    * {  
    *    "id": 5,
    *    "city_identity_card_id": 5,
    *    "identity_card": "165134-1R",
    *    "last_name": "PINTO",
    *    "mothers_last_name": "ROJAS",
    *    "first_name": "JUAN",
    *    "second_name": "ROBERTO",
    *    "surname_husband": null,
    *    "birth_date": "1980-05-02",
    *    "gender": "M",
    *    "civil_status": "C",
    *    "phone_number": "2254101",
    *    "cell_phone_number": "76543210",
    *    "created_at": "2020-03-03 18:16:43",
    *    "updated_at": "2020-03-03 18:16:43"
    * }
    */
    public function show($id)
    {
        return PersonalReference::findOrFail($id);
    }

    /**
    * Actualizar Persona de Referencia
    * Actualizar datos principales de Persona de Referencia
    * @urlParam id required ID de Persona de Referencia. Example: 5
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 5
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam last_name string required Apellido paterno. Example: PINTO
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAZ
    * @bodyParam first_name string required Primer nombre. Example: JUAN
    * @bodyParam second_name string Segundo nombre. Example: ROBERTO
    * @bodyParam birth_date date required Fecha de nacimiento (AÑO-MES-DÍA). Example: 1980-05-02
    * @bodyParam gender string required Género (M,F). Example: M
    * @bodyParam civil_status string required Estado civil (S,C,D,V). Example: C
    * @bodyParam phone_number integer Número de teléfono fijo. Example: 2254101
    * @bodyParam cell_phone_number integer Número de celular. Example: 76543210
    * @authenticated
    * @response
    * {  
    *    "id": 5,
    *    "city_identity_card_id": 5,
    *    "identity_card": "165134-1R",
    *    "last_name": "PINTO",
    *    "mothers_last_name": "ROJAZ",
    *    "first_name": "JUAN",
    *    "second_name": "ROBERTO",
    *    "surname_husband": null,
    *    "birth_date": "1980-05-02",
    *    "gender": "M",
    *    "civil_status": "C",
    *    "phone_number": "2254101",
    *    "cell_phone_number": "76543210",
    *    "created_at": "2020-03-03 18:16:43",
    *    "updated_at": "2020-03-03 18:25:35"
    * }
    */
    public function update(PersonalReferenceForm $request, $id)
    {
        $personal_reference = PersonalReference::findOrFail($id);
        $personal_reference->fill($request->all());
        $personal_reference->save();
        return  $personal_reference;
    }

    /**
    * Eliminar una Persona de Referencia
    * @urlParam id required ID de Persona de Referencia. Example: 5
    * @authenticated
    * @response
    * {  
    *    "id": 5,
    *    "city_identity_card_id": 5,
    *    "identity_card": "165134-1R",
    *    "last_name": "PINTO",
    *    "mothers_last_name": "ROJAZ",
    *    "first_name": "JUAN",
    *    "second_name": "ROBERTO",
    *    "surname_husband": null,
    *    "birth_date": "1980-05-02",
    *    "gender": "M",
    *    "civil_status": "C",
    *    "phone_number": "2254101",
    *    "cell_phone_number": "76543210",
    *    "created_at": "2020-03-03 18:16:43",
    *    "updated_at": "2020-03-03 18:25:35"
    * }
    */
    public function destroy($id)
    {
        $personal_reference = PersonalReference::findOrFail($id);
        $personal_reference->delete();
        return $personal_reference;
    }
}
