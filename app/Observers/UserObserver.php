<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Util;
use App\User;
use App\RecordType;

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
        $this->save_record($user, 'sistema', 'registró');
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
            $action .= (' [' . Util::translate($key) . '] ' . Util::bool_to_string($old[$key]) . ' -> ' . Util::bool_to_string($user[$key]));
            if (next($updated_values)) {
                $action .= ', ';
            }
        }
        $this->save_record($user, 'sistema', $action);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        $this->save_record($user, 'sistema', 'eliminó usuario: ' . $user->full_name);
    }

    private function save_record($user, $type, $action)
    {
        $logged_user = Auth::user();
        $record_type = RecordType::whereName($type)->first();
        if ($logged_user && $record_type) {
            $user->records()->create([
                'user_id' => $logged_user->id,
                'record_type_id' => $record_type->id,
                'action' => $action
            ]);
        }
    }
}
