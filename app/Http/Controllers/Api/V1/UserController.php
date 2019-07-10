<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Requests\UserForm;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    if ($request->has('status')) {
      $users = User::whereStatus($request->input('status'));
    } else {
      $users = new User();
    }
    if ($request->has('sortBy')) {
      if ($request->sortBy != 'null') {
        $users = $users->orderBy($request->sortBy, $request->input('direction') ?? 'asc');
      }
    }
    return $users->paginate($request->input('per_page') ?? 10);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(UserForm $request)
  {
    return User::create($request->all());
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return User::findOrFail($id);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function update(UserForm $request, $id)
  {
    $user = User::findOrFail($id);
    $user->fill($request->all());
    $user->save();
    return $user;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
