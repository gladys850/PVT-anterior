<?php

namespace App\Http\Controllers\Api\V1;

use Util;
use App\Affiliate;
use App\RecordType;
use App\User;
use App\Category;
use App\Degree;
use App\City;
use App\Hierarchy;
use App\AffiliateState;
use App\AffiliateStateType;
use App\Spouse;
use App\Address;
use App\Contribution;
use App\Unit;
use App\Loan;
use App\LoanGlobalParameter;
use App\ProcedureType;
use App\Http\Requests\AffiliateForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Events\FingerprintSavedEvent;
use Illuminate\Support\Facades\Storage;
use Carbon\CarbonImmutable;

/** @group Datos Afiliado
* Datos de los afiliados y métodos para obtener y establecer sus relaciones
*/
class AffiliateController extends Controller
{
    /**
    * Lista de afiliados
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
    *             "id": 3561,
    *             "user_id": 1,
    *             "affiliate_state_id": 5,
    *             "city_identity_card_id": 4,
    *             "city_birth_id": 4,
    *             "degree_id": 32,
    *             "unit_id": 19,
    *             "category_id": 8,
    *             "pension_entity_id": 2,
    *             "identity_card": "2234288",
    *             "registration": "540328TMS",
    *             "type": "Comando",
    *             "last_name": "TORRE",
    *             "mothers_last_name": "MULLISACA",
    *             "first_name": "SIXTO",
    *             "second_name": null,
    *             "surname_husband": null,
    *             "gender": "M",
    *             "civil_status": "C",
    *             "birth_date": "1954-03-28",
    *             "date_entry": "1983-01-01",
    *             "date_death": null,
    *             "reason_death": "",
    *             "date_derelict": null,
    *             "reason_derelict": null,
    *             "change_date": "2016-07-01",
    *             "phone_number": "",
    *             "cell_phone_number": "(720)-15937",
    *             "afp": true,
    *             "nua": 26271503,
    *             "item": 32706,
    *             "created_at": "2017-06-01 10:43:12",
    *             "updated_at": "2018-10-26 10:04:49",
    *             "deleted_at": null,
    *             "service_years": null,
    *             "service_months": null,
    *             "death_certificate_number": "",
    *             "due_date": null,
    *             "is_duedate_undefined": true,
    *             "affiliate_registration_number": null,
    *             "file_code": null,
    *             "picture_saved": true,
    *             "fingerprint_saved": true,
    *             "full_name": "SIXTO TORRE MULLISACA"
    *         }, {}
    *     ],
    *     "first_page_url": "http://127.0.0.1/api/v1/affiliate?page=1",
    *     "from": 1,
    *     "last_page": 5760,
    *     "last_page_url": "http://127.0.0.1/api/v1/affiliate?page=5760",
    *     "next_page_url": "http://127.0.0.1/api/v1/affiliate?page=2",
    *     "path": "http://127.0.0.1/api/v1/affiliate",
    *     "per_page": 10,
    *     "prev_page_url": null,
    *     "to": 10,
    *     "total": 57592
    * }
    */
    public function index(Request $request)
    {
        $data = Util::search_sort(new Affiliate(), $request);
        foreach ($data as $affiliate) {
            $this->append_data($affiliate);
        }
        return $data;
    }

    /**
    * Nuevo afiliado
    * Inserta nuevo afiliado
    * @bodyParam first_name string required Primer nombre. Example: JUAN
    * @bodyParam last_name string required Apellido paterno. Example: PINTO
    * @bodyParam gender string required Género (M,F). Example: M
    * @bodyParam birth_date date required Fecha de nacimiento (AÑO-MES-DÍA). Example: 1980-05-02
    * @bodyParam city_birth_id integer required ID de ciudad de nacimiento. Example: 10
    * @bodyParam city_identity_card_id integer required ID de ciudad del CI. Example: 4
    * @bodyParam civil_status string required Estado civil (S,C,D,V). Example: C
    * @bodyParam identity_card string required Carnet de identidad. Example: 165134-1L
    * @bodyParam affiliate_state_id integer ID de estado de afiliado. Example: 2
    * @bodyParam degree_id integer ID del grado policial. Example: 4
    * @bodyParam unit_id integer ID de unidad de destino. Example: 7
    * @bodyParam category_id integer ID de categoría. Example: 9
    * @bodyParam pension_entity_id integer ID de entidad de pensiones. Example: 1
    * @bodyParam registration string Matrícula. Example: 870914VBW
    * @bodyParam type string Tipo de destino (Comando, Batallón). Example: Comando
    * @bodyParam second_name string Segundo nombre. Example: ROBERTO
    * @bodyParam mothers_last_name string Apellido materno. Example: ROJAS
    * @bodyParam surname_husband string Apellido de casada. Example: PAREDES
    * @bodyParam date_entry date Fecha de ingreso a la policía. Example: 1980-05-20
    * @bodyParam date_death date Fecha de fallecimiento. Example: 2018-09-21
    * @bodyParam death_certificate_number date Fecha de certificado de defunción. Example: 2018-09-23
    * @bodyParam reason_death string Causa de fallecimiento. Example: Embolia
    * @bodyParam date_derelict date Fecha de baja de la policía. Example: 2017-12-30
    * @bodyParam reason_derelict string Causa de baja de la policía. Example: Proceso administrativo
    * @bodyParam due_date date Fecha de vencimiento del CI. Example: 2018-01-05
    * @bodyParam is_duedate_undefined boolean Si la fecha de vencimiento de CI es indefinido . Example: false
    * @bodyParam change_date date Fecha de cambio. Example: 2015-02-03
    * @bodyParam phone_number integer Número de teléfono fijo. Example: 2254101
    * @bodyParam cell_phone_number array Números de celular. Example: [76543210,65432101]
    * @bodyParam afp boolean Si el afiliado aporta a AFP(true) o SENASIR(false). Example: true
    * @bodyParam nua integer Número NUA. Example: 26271503
    * @bodyParam item integer Número de ítem policial. Example: 32706
    * @bodyParam service_years integer Años de servicio. Example: 6
    * @bodyParam service_months integer Meses de servicio. Example: 4
    * @bodyParam affiliate_registration_number integer Número único de registro de afiliado. Example: 10512
    * @bodyParam file_code string Código de folder de afiliado. Example: AFW-12
    * @authenticated
    * @response
    * {
    *     "first_name": "JUAN",
    *     "last_name": "PINTO",
    *     "gender": "M",
    *     "birth_date": "1980-05-02",
    *     "city_birth_id": 10,
    *     "city_identity_card_id": 4,
    *     "civil_status": "C",
    *     "identity_card": "165134-1L",
    *     "affiliate_state_id": 2,
    *     "degree_id": 4,
    *     "unit_id": 7,
    *     "category_id": 9,
    *     "pension_entity_id": 1,
    *     "registration": "870925VBW",
    *     "type": "Comando",
    *     "second_name": "ROBERTO",
    *     "mothers_last_name": "ROJAS",
    *     "surname_husband": "PAREDES",
    *     "date_entry": "1980-05-20",
    *     "date_death": "2018-09-21",
    *     "reason_death": "EMBOLIA",
    *     "date_derelict": "2017-12-30",
    *     "reason_derelict": "Proceso administrativo",
    *     "due_date": "2018-01-05",
    *     "is_duedate_undefined": false,
    *     "change_date": "2015-02-03",
    *     "phone_number": 2254101,
    *     "cell_phone_number": "[76543210,65432101]",
    *     "afp": true,
    *     "nua": 26271503,
    *     "item": 32706,
    *     "updated_at": "2020-02-07 15:31:31",
    *     "created_at": "2020-02-07 15:31:31",
    *     "id": 58662
    * }
    */
    public function store(AffiliateForm $request)
    {
        return Affiliate::create($request->all());
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Affiliate  $affiliate
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $this->append_data($affiliate);
        $affiliate->dead = $affiliate->dead;
        $affiliate->defaulted = $affiliate->defaulted;
        return $affiliate;
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Affiliate  $affiliate
    * @return \Illuminate\Http\Response
    */
    public function update(AffiliateForm $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->fill($request->all());
        $affiliate->save();
        return  $affiliate;
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Affiliate  $affiliate
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        //
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->delete();
        return $affiliate;
    }

    public function fingerprint_saved(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        event(new FingerprintSavedEvent($affiliate, $user, $request->success));
        return response()->json([
            'message' => 'Message broadcasted'
        ], 200);
    }

    public function fingerprint_updated(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $record_type = RecordType::whereName('datos-personales')->first();
        if ($record_type) {
            $affiliate->records()->create([
                'user_id' => Auth::user()->id,
                'record_type_id' => $record_type->id,
                'action' => 'inició la captura de huellas'
            ]);
            return $affiliate->records()->latest()->first();
        }
        abort(404);
    }

    private function append_data($affiliate) {
        $affiliate->picture_saved = $affiliate->picture_saved;
        $affiliate->fingerprint_saved = $affiliate->fingerprint_saved;
        $affiliate->full_name = $affiliate->full_name;
    }

    public function PictureImageprint(Request $request, $id)
    {
        $files = [];
        $base_path = 'picture/';
        $fingerprint_files = ['_perfil.jpg'];
        foreach ($fingerprint_files as $file) {
            if (Storage::disk('ftp')->exists($base_path . $id . $file)) {
                array_push($files, [
                    'name' => $id . $file,
                    'content' => base64_encode(Storage::disk('ftp')->get($base_path . $id . $file)),
                    'format' => Storage::disk('ftp')->mimeType($base_path . $id . $file)
                ]);
            }
        }
        return $files;
    }

    public function FingerImageprint($id)
    {
        $files = [];
        $base_path = 'picture/';
        $fingerprint_files = ['_left_four.png', '_right_four.png', '_thumbs.png'];
        foreach ($fingerprint_files as $file) {
            if (Storage::disk('ftp')->exists($base_path . $id . $file)) {
                array_push($files, [
                    'name' => $id . $file,
                    'content' => base64_encode(Storage::disk('ftp')->get($base_path . $id . $file)),
                    'format' => Storage::disk('ftp')->mimeType($base_path . $id . $file)
                ]);
            }
        }
        return $files;
    }

    public function picture_save(Request $request, $id)
    {
    //$picture=$request->all();
    $base_path = 'picture/';
    $affiliate = Affiliate::findOrFail($id);
    $code = $affiliate->id;
    $image = $request->image;   
    $image = str_replace('data:image/jpeg;base64,', '', $image);
    $image = str_replace(' ', '+', $image);
    $imageName = $code.'_perfil.'.'jpg';

    Storage::disk('ftp')->put($base_path.$imageName, base64_decode($image));

    }
    //get information spouse
    public function spouse_get($affiliate_id){
        $spouse = Spouse::where('affiliate_id',$affiliate_id)->first();
        if(!$spouse) abort (404);
        return ($spouse);
    }  
    //addresses
    public function addresses_get($affiliate_id){
        $affiliate=Affiliate::find($affiliate_id);
        $addreses = $affiliate->addresses()->orderByDesc('created_at')->get();
        return $addreses;
    }
    public function addresses_update(Request $request,$affiliate_id){
       // $addresses=[8570,10371,18];
        foreach($request->addresses as $dir) {
            if(!Address::whereId($dir)->exists()) abort (404); 
        }
        return Affiliate::find($affiliate_id)->addresses()->sync($request->addresses); 
    }

    /**
    * Boletas de pago
    * Devuelve el listado de las boletas de pago de un afiliado
    * @urlParam id ID de afiliado
    * @queryParam sortBy Vector de ordenamiento. Example: [month_year]
    * @queryParam sortDesc Vector de orden descendente(true) o ascendente(false). Example: [true]
    * @queryParam per_page Número de datos por página. Example: 3
    * @queryParam page Número de página. Example: 1
    * @authenticated
    */
    public function get_contributions(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $filters = [
            'affiliate_id' => $affiliate->id
        ];
        $contributions = Util::search_sort(new Contribution(), $request, $filters);
        if ($request->has('city_id')) {
            $is_latest = false;
            $city = City::findOrFail($request->city_id);
            $offset_day = LoanGlobalParameter::latest()->first()->offset_day;
            $now = CarbonImmutable::now();
            if ($now->day <= $offset_day || $city->name != 'LA PAZ') {
                $before_month = 2;
            } else {
                $before_month = 1;
            }
            $current_ticket = CarbonImmutable::parse($contributions[0]->month_year);
            if ($now->startOfMonth()->diffInMonths($current_ticket->startOfMonth()) <= $before_month) {
                foreach ($contributions as $i => $ticket) {
                    $is_latest = true;
                    if ($ticket != $contributions->last()) {
                        $current_ticket = CarbonImmutable::parse($ticket->month_year);
                        $next_ticket = CarbonImmutable::parse($contributions[$i+1]->month_year);
                        if ($current_ticket->startOfMonth()->diffInMonths($next_ticket->startOfMonth()) !== 1) {
                            $is_latest = false;
                            break;
                        }
                    }
                }
            } else {
                $is_latest = false;
            }
            $contributions = collect([
                'valid' => $is_latest,
                'diff_months' => $before_month
            ])->merge($contributions);
        }
        return $contributions;
    }

    public function get_category($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        return $affiliate->category;
    }
    public function get_degree($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        return $affiliate->degree;
    }
    public function get_unit($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        return $affiliate->unit;
    }

    public function get_loans(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $type = Util::get_bool($request->guarantor) ? 'guarantors' : 'lenders';
        $relations[$type] = [
            'affiliate_id' => $affiliate->id
        ];
        if ($request->has('state')) {
            $relations['state'] = [
                'id' => $request->state
            ];
        }
        return Util::search_sort(new Loan(), $request, [], $relations, ['id']);
    }

    public function get_state($id)
    {
        $affiliate = Affiliate::findOrFail($id);
        if ($affiliate->affiliate_state) $affiliate->affiliate_state->affiliate_state_type;
        return response()->json($affiliate->affiliate_state);
    }

    public function verify_guarantor(Request $request,$affiliate_id){
        //$ballots=[200]; $bonuses=[24]; $new_quota=100;
        $affiliate=Affiliate::find($affiliate_id)->active_guarantees();$sum_quota=0;$state = false;
        foreach($affiliate as $affiliate_loans){ 
            $sum_quota+= $affiliate_loans->estimated_quota; 
        }
        $loan = new Loan();
        $liquid_qualification = $loan->liquid_qualification($request->ballots,$request->bonuses);// se debe modificar
        $qualify = $liquid_qualification - $sum_quota - ($request->new_quota);
        $loan_global_parameter = LoanGlobalParameter::get()->last();
        if($qualify>$loan_global_parameter->livelihood_amount){
            $qualify = $qualify;
            $state = true; 
        } 
        return [
            'qualify'=>$qualify,
            'state'=>$state
        ];      
    }
    public function cpop($affiliate_id){
        $affiliate = new Affiliate();
        $cpop = $affiliate->verify_cpop($affiliate_id);
        if($cpop==true){
            $state_cpop = true;
        }else{
            $state_cpop = $cpop;
        }
        return ['state_cpop'=>$state_cpop];
    }
    public function get_loan_modality(Request $request,$id){
        $affiliate = Affiliate::findOrFail($id);
        $modality_name = ProcedureType::findOrFail($request->procedure_type_id)->name;
        $loan = new Loan();
        return $loan->get_modality($modality_name,$affiliate);
       
    }
}