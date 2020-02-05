<?php

namespace App\Observers;

use App\Affiliate;
use App\Helpers\Util;

class AffiliateObserver
{
    /**
    * Handle the contract "created" event.
    *
    * @param  \App\Affiliate  $contract
    * @return void
    */
    public function created(Affiliate $object)
    {
        Util::save_record($object, 'datos-personales', 'registró');
    }
    /**
    * Handle the affiliate "updating" event.
    *
    * @param  \App\Affiliate  $Affiliate
    * @return void
    */
    public function updating(Affiliate $object)
    {
        Util::save_record($object, 'datos-personales', Util::concat_action($object));
    }
    /**
    * Handle the affiliate "deleted" event.
    *
    * @param  \App\Affiliate  $Affiliate
    * @return void
    */
    public function deleted(Affiliate $object)
    {
        Util::save_record($object, 'datos-personales', 'eliminó afiliado: ' . $object->full_name);
    }
}