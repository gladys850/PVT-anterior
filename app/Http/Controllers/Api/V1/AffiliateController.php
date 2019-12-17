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
use App\Http\Requests\AffiliateForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Events\FingerprintSavedEvent;
use Illuminate\Support\Facades\Storage;


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
        $affiliate->picture_saved;
        $affiliate->fingerprint_saved;
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
    public function get_contributions($affiliate_id,$quantity){
        if(!Contribution::whereAffiliate_id($affiliate_id)->exists()) {
            abort (404); 
        }else{
            $contribution=Contribution::whereAffiliate_id($affiliate_id)->orderBy('month_year','desc')->get();
            if(count($contribution)>$quantity){
                $c=0; $ballots=[];
                while($c<$quantity){ $ballots[$c]=$contribution[$c]; $c++; }
                return $ballots;
            }
            return abort(404); 
        }
    }
    // Recabar los ultimos bonos de la ultima boleta
    public function last_bonuses_ballot($affiliate_id){
        $bonus=Contribution::whereAffiliate_id($affiliate_id)->get()->last();
        if($bonus){
            $data_bonus=[];
            $data_bonus[1]=$bonus->border_bonus;
            $data_bonus[2]=$bonus->east_bonus;
            $data_bonus[3]=$bonus->public_security_bonus;
            $data_bonus[4]=$bonus->seniority_bonus;
            return $data_bonus;
        }else{
            return abort(404); 
        }
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
    public function last_three_loans($affiliate_id){
        $affiliate = Affiliate::find($affiliate_id);
        $loans_affiliates=$affiliate->loans;
        if(count($loans_affiliates)<=3){
            return $loans_affiliates;
        }else{
            $c=0; $loans=[];
            while($c<3){ 
                $loans[$c]=$loans_affiliates[$c]; 
                $c++; 
            }
            return $loans;
        }
    }
    public function get_state($affiliate_id)
    {
        $affiliate=Affiliate::find($affiliate_id);
        if($affiliate->affiliate_state_id!=null){
            $state=$affiliate->affiliate_state;
            $state_type= $affiliate->affiliate_state->affiliate_state_type()->first();
            return [
                'state'=>$state,
                'state_type'=>$state_type
            ];  
        }return [];
    }
}