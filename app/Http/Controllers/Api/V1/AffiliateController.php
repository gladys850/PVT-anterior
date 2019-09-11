<?php

namespace App\Http\Controllers\Api\V1;

use App\Affiliate;
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
        return $affiliates->paginate($request->input('per_page') ?? 10);
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
    }
}
