<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Record;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = Record::query();
        if ($request->has('user_id')) {
            $records = $records->whereUserId($request->user_id);
        }
        if ($request->has('sortBy')) {
            if (count($request->sortBy) > 0 && count($request->sortDesc) > 0) {
                foreach ($request->sortBy as $i => $sort) {
                    $records = $records->orderBy($sort, filter_var($request->sortDesc[$i], FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc');
                }
            }
        }
        return $records->paginate($request->input('per_page') ?? 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $record = new Record();
            $record->fill($request->all());
            $record->user_id = $user->id;
            $record->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
