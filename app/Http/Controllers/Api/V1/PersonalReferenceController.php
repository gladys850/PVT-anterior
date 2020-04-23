<?php

namespace App\Http\Controllers\Api\V1;
use App\PersonalReference;
use App\Http\Requests\PersonalReferenceForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Util;

/** @group Referencias Personales
* Datos de las personas de referencia para trámites de préstamos
*/
class PersonalReferenceController extends Controller
{
    /**
    * Lista de Personas de Referencia
    * Devuelve el listado con los datos paginados
    * @queryParam search Parámetro de búsqueda. Example: MARIA
    * @queryParam sortBy Vector de ordenamiento. Example: [last_name]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [0]
    * @queryParam per_page Número de datos por página. Example: 8
    * @queryParam page Número de página. Example: 1
    * @authenticated
    * @responseFile responses/personal_reference/index.200.json
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
    * @bodyParam phone_number integer Número de teléfono fijo. Example: 2254101
    * @bodyParam cell_phone_number integer Número de celular. Example: 76543210
    * @authenticated
    * @responseFile responses/personal_reference/store.200.json
    */
    public function store(PersonalReferenceForm $request)
    {
        return PersonalReference::create($request->all());
    }

    /**
    * Detalle de Referencia Personal
    * Devuelve el detalle de una Referencia Personal mediante su ID
    * @urlParam personal_reference required ID de Referencia Personal. Example: 5
    * @authenticated
    * @responseFile responses/personal_reference/show.200.json
    */
    public function show(PersonalReference $personal_reference)
    {
        return $personal_reference;
    }

    /**
    * Actualizar Persona de Referencia
    * Actualizar datos principales de Persona de Referencia
    * @urlParam personal_reference required ID de Persona de Referencia. Example: 5
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 5
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam last_name string required Apellido paterno. Example: PINTO
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAZ
    * @bodyParam first_name string required Primer nombre. Example: JUAN
    * @bodyParam second_name string Segundo nombre. Example: ROBERTO
    * @bodyParam phone_number integer Número de teléfono fijo. Example: 2254101
    * @bodyParam cell_phone_number integer Número de celular. Example: 76543210
    * @authenticated
    * @responseFile responses/personal_reference/update.200.json
    */
    public function update(PersonalReferenceForm $request, PersonalReference $personal_reference)
    {
        $personal_reference->fill($request->all());
        $personal_reference->save();
        return  $personal_reference;
    }

    /**
    * Eliminar una Persona de Referencia
    * @urlParam personal_reference required ID de Persona de Referencia. Example: 5
    * @authenticated
    * @responseFile responses/personal_reference/destroy.200.json
    */
    public function destroy(PersonalReference $personal_reference)
    {
        $personal_reference->delete();
        return $personal_reference;
    }
}
