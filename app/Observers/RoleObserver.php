<?php

namespace App\Observers;

use App\Role;
use App\Permission;
use App\Helpers\Util;

class RoleObserver
{
    public function pivotAttached($model, $relationName, $pivotIds, $pivotIdsAttributes)
    {
        Util::save_record(Role::find($model['id']), 'sistema', Util::pivot_action($relationName, $pivotIds, 'agregó'));
    }

    public function pivotDetached($model, $relationName, $pivotIds)
    {
        Util::save_record(Role::find($model['id']), 'sistema', Util::pivot_action($relationName, $pivotIds, 'eliminó'));
    }
}