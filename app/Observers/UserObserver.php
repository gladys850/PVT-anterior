<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Util;
use App\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $object)
    {
        Util::save_record($object, 'sistema', 'registró');
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updating(User $object)
    {
        Util::save_record($object, 'sistema', Util::concat_action($object));
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $object)
    {
        Util::save_record($object, 'sistema', 'eliminó usuario: ' . $object->full_name);
    }
}
