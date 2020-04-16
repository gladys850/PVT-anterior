<?php

namespace App\Http\Controllers\Api\V1;
use App\Address;
use App\Http\Requests\AddressForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/** @group Direcciones
* Datos de las direcciones de los afiliados y de aquellas relacionadas con los trámites
*/
class AddressController extends Controller
{
    /**
    * Nueva dirección
    * Inserta nueva dirección
    * @bodyParam city_address_id integer ID de ciudad del CI. Example: 4
    * @bodyParam zone string Zona. Example: Chuquiaguillo
    * @bodyParam street string Calle. Example: Av. Panamericana
    * @bodyParam number_address integer Número de casa. Example: 45
    * @bodyParam latitude float Latitud de acuerdo a OpenStreet Maps. Example: -16.495244595604056
    * @bodyParam longitude float Longitud de acuerdo a OpenStreet Maps. Example: -68.13450627055796
    * @authenticated
    * @response
    * {
    *     "city_address_id": 4,
    *     "zone": "Chuquiaguillo",
    *     "street": "Av. Panamericana",
    *     "number_address": 43,
    *     "latitude": -16.495244595604056,
    *     "longitude": -68.13450627055796,
    *     "updated_at": "2020-02-14 14:38:00",
    *     "created_at": "2020-02-14 14:38:00",
    *     "id": 11805
    * }
    */
    public function store(AddressForm $request)
    {
        return Address::create($request->all());
    }

    /**
    * Actualizar dirección
    * Actualizar los datos de una dirección existente
    * @urlParam address required ID de dirección. Example: 11805
    * @bodyParam city_address_id integer ID de ciudad del CI. Example: 4
    * @bodyParam zone string Zona. Example: Chuquiaguillo
    * @bodyParam street string Calle. Example: Av. Panamericana
    * @bodyParam number_address integer Número de casa. Example: 45
    * @bodyParam latitude float Latitud de acuerdo a OpenStreet Maps. Example: -16.495244595604056
    * @bodyParam longitude float Longitud de acuerdo a OpenStreet Maps. Example: -68.13450627055796
    * @authenticated
    * @response
    * {
    *     "city_address_id": 4,
    *     "zone": "Chuquiaguillo",
    *     "street": "Av. Panamericana",
    *     "number_address": 43,
    *     "latitude": -16.495244595604056,
    *     "longitude": -68.13450627055796,
    *     "updated_at": "2020-02-14 14:38:00",
    *     "created_at": "2020-02-14 14:38:00",
    *     "id": 11805
    * }
    */
    public function update(AddressForm $request, Address $address)
    {
        $address->fill($request->all());
        $address->save();
        return $address;
    }

    /**
    * Eliminar dirección
    * Eliminar una dirección solo en caso de que no este relacionada ningún trámite
    * @urlParam address required ID de dirección. Example: 1077
    * @authenticated
    * @response
    * {
    *     "id": 1077,
    *     "city_address_id": 4,
    *     "zone": "Villa Tejada Triangular-El Alto",
    *     "street": "11",
    *     "number_address": "112",
    *     "created_at": "2018-09-06 15:57:42",
    *     "updated_at": "2018-09-06 17:44:45",
    *     "updated_at":" 2020-02-14 14:38:00",
    *     "created_at":" 2020-02-14 14:38:00"
    * }
    */
    public function destroy(Address $address)
    {
        $address->delete();
        return $address;
    }
}
