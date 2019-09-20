<?php

namespace App\Http\Controllers\Api\V1;

use App\Affiliate;
use App\Category;
use App\Degreee;
use App\City;
use App\Hierarchy;
use App\AffiliateState;
use App\AffiliateStateType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Requests\AffiliateForm;

class AffiliateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       //return Affiliate::paginate(10);
       $affiliates = Affiliate::with('city_identity_card')->with('degree')->with('category')->with('affiliate_state')->with('city_birth');
        if ($request->has('search')) {
            if ($request->search != 'null' && $request->search != '') {
              $search = $request->search;
              $affiliates = $affiliates->where(function ($query) use ($search) {
                foreach (Schema::getColumnListing(Affiliate::getTableName()) as $column) { 
                  $query = $query->orWhere($column, 'ilike', '%' . $search . '%');
                }
              });
            }
        }
        if ($request->has('sortBy')) {
          if ($request->sortBy != 'null') {
            $affiliates = $affiliates->orderBy($request->sortBy, $request->input('direction') ?? 'asc');
          }
        }
        return $affiliates->paginate($request->input('per_page') ?? 10);
    }
    //foreach($affiliates as $a) {$a->affiliate_state ? $a->affiliate_state_type : null;}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AffiliateForm $request)
    {
        return Affiliate::create($request->all());

       /* Affiliate::create([
          'user_id' => Auth::user()->id,
          'identity_card' => $request->identity_card,
          'last_name' => $request->last_name,
          'mothers_last_name' => $request->mothers_last_name,
          'first_name' => $request->first_name,
          'second_name' => $request->second_name,
          'city_identity_card_id' => $request->city_identity_card_id,
          'surname_husband' => $request->surname_husband,
          'nua' => $request->nua,
          'phone_number' => $request->phone_number,
          'cell_phone_number' => $request->cell_phone_number,
          'due_date' => $due_date,
          'gender' => $request->gender,
          'birth_date' => $request->birth_date,
          'civil_status' => $request->civil_status,
          'type' => $request->type,
          'category_id' => $request->category_id,
          'pension_entity_id' => $request->pension_entity_id,
          'degree_id' => $request->degree_id,
          'is_duedate_undefined' => $verificate,
          'city_birth_id' => $request->city_birth_id
      ]);*/
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Affiliate  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Affiliate::findOrFail($id);
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
        //
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
}
