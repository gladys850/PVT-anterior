<?php

namespace App\Observers;

use App\LoanContributionAdjust;
use App\Helpers\Util;
class LoanContributionAdjustObserver
{
    
    /**
    * Handle the contract "created" event.
    *
    * @param  \App\LoanContributionAdjust  $contract
    * @return void
    */
    public function created(LoanContributionAdjust $adjustContribution)
    {
       Util::save_record($adjustContribution, 'ajuste-contribuciones', 'Registró contribución : '. $adjustContribution->id.' de fecha '.$adjustContribution->period_date);
    }
    /**
    * Handle the affiliate "updating" event.
    *
    * @param  \App\LoanContributionAdjust  $Affiliate
    * @return void
    */
    public function updated(LoanContributionAdjust $adjustContribution)
    {   
        Util::save_record($adjustContribution, 'ajuste-contribuciones', 'Actualizó contribución : '. $adjustContribution->id.' de fecha '.$adjustContribution->period_date);
    }
    /**
    * Handle the affiliate "deleted" event.
    *
    * @param  \App\Affiliate  $Affiliate
    * @return void
    */
    public function deleted(LoanContributionAdjust $adjustContribution)
    {
    
        Util::save_record($adjustContribution, 'ajuste-contribuciones', 'eliminó afiliado: '. $adjustContribution->id.' de fecha '.$adjustContribution->period_date);
    }
    
}
