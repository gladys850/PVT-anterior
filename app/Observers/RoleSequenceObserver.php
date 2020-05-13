<?php

namespace App\Observers;

use App\Role;
use App\ProcedureType;
use App\RoleSequence;
use Util;

class RoleSequenceObserver
{
    /**
     * Handle the role sequence "created" event.
     *
     * @param  \App\RoleSequence  $roleSequence
     * @return void
     */
    public function created(RoleSequence $object)
    {
        $procedure_type = ProcedureType::find($object->procedure_type_id);
        Util::save_record($object, 'sistema', 'registró flujo: '. Role::find($object->role_id)->display_name . ' -> ' . Role::find($object->next_role_id)->display_name, $procedure_type);
    }
}
