<?php

namespace App\Observers;

use Util;
use App\User;
use Illuminate\Http\Request;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        $old = new User();
        $old->fill($user->getOriginal());
        $action = 'editó';
        $updated_values = $user->getDirty();
        foreach ($updated_values as $key => $value) {
            $action .= (' [' . Util::translate($key) . '] ' . $old[$key] . ' -> ' . $user[$key]);
            if (next($updated_values)) {
                $action .= ', ';
            } else {
                $action .= '.';
            }
        }
        $controller = new App\Http\Controllers\Api\V1\RecordController;
        $controller->store(Request::create(null, null, [
            'action'=> $action,
            'recordable_type' => 'users',
            'recordable_id' => $user->id
        ]));
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
