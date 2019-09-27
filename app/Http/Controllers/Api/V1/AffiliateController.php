<?php

namespace App\Http\Controllers\Api\V1;

use App\Affiliate;
use App\User;
use App\Category;
use App\Degreee;
use App\City;
use App\Hierarchy;
use App\AffiliateState;
use App\AffiliateStateType;
use App\Http\Requests\AffiliateForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Events\FingerprintSavedEvent;

class AffiliateController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $affiliates = Affiliate::query();
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
        $affiliates = $affiliates->paginate($request->per_page ?? 10);
        foreach ($affiliates as $affiliate) {
            $this->append_data($affiliate);
        }
        return $affiliates;
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
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
        return $affiliate;
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Affiliate  $affiliate
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
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

    public function fingerprint_saved(Request $request, $id)
    {
        $affiliate = Affiliate::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        event(new FingerprintSavedEvent($affiliate, $user, $request->success));
        return response()->json([
            'message' => 'Message broadcasted'
        ], 200);
    }

    private function append_data($affiliate) {
        $affiliate->picture_saved;
        $affiliate->fingerprint_saved;
    }
}