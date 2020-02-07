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

class AffiliateController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
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
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(AffiliateForm $request)
    {
        return Affiliate::create($request->all());
        //$affiliate->phone_number = trim(implode(",", $request->phone_number));
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

    //ballots
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

    public function get_category($affiliate_id)
    {
        $affiliate=Affiliate::find($affiliate_id);
        if($affiliate->category_id!=null){
            return $affiliate->category->name; 
        }return []; 
          
    }
    public function get_degree($affiliate_id)
    {
        $affiliate=Affiliate::find($affiliate_id);
        if($affiliate->degree_id!=null){
            return $affiliate->degree->name; 
        } return [];  
    }
    public function get_unit($affiliate_id)
    {
        $affiliate=Affiliate::find($affiliate_id);
        if($affiliate->unit_id!=null){
            return $affiliate->unit->name;   
        }return [];
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
    public function get_modality(Request $request,$id){
        $affiliate = Affiliate::findOrFail($id);
        $modality_name = ProcedureType::findOrFail($request->procedure_type_id)->name;
        $loan = new Loan();
        return $loan->get_modality($modality_name,$affiliate);
       
    }
}