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
    public function created(Affiliate $affiliate)
    {
      Util::save_action('Afiliado Registrado: ' . $this->get_title($affiliate));
    }
    /**
     * Handle the affiliate "updating" event.
     *
     * @param  \App\Affiliate  $Affiliate
     * @return void
     */
    public function updating(Affiliate $changed)
    {
      if($changed->isDirty){
        $old = new Affiliate();
        $old->fill($changed->getOriginal());
        $changes ='Datos cambiados de afiliado' . $this->get_title($changed) . ':';
        foreach($changed->getDirty() as $key => $value){
          if ($key == ''){
            $changes .= (' [' . $key . '] ' . $old->phone_number->name . ' => ' . $changed->phone_number->name . ',');
          } elseif ($key == 'city_identity_card') {
            $changes .= (' [' . $key . '] ' . $old->city_identity_card->name . ' => ' . $changed->city_identity_card->name . ',');
          } elseif ($key == 'identity_card') {
            $changes .= (' [' . $key . '] ' . $old->identity_card->name . ' => ' . $changed->identity_card->name . ',');
          } elseif ($key == 'first_name') {
            $changes .= (' [' . $key . '] ' . $old->first_name->name . ' => ' . $changed->first_name->name . ',');
          } elseif ($key == 'second_name') {
            $changes .= (' [' . $key . '] ' . $old->second_name->name . ' => ' . $changed->second_name->name . ',');
          } elseif ($key == 'last_name') {
            $changes .= (' [' . $key . '] ' . $old->last_name->name . ' => ' . $changed->last_name->name . ',');
          } elseif ($key == 'mothers_last_name') {
            $changes .= (' [' . $key . '] ' . $old->mothers_last_name->name . ' => ' . $changed->mothers_last_name->name . ',');
          } elseif ($key == 'gender') {
            $changes .= (' [' . $key . '] ' . $old->civil_status->name . ' => ' . $changed->civil_status->name . ',');
          } elseif ($key == 'civil_status') {
            $changes .= (' [' . $key . '] ' . $old->civil_status->name . ' => ' . $changed->civil_status->name . ',');
          } elseif ($key == 'birth_date') {
            $changes .= (' [' . $key . '] ' . $old->birth_date->name . ' => ' . $changed->birth_date->name . ',');
          } elseif ($key == 'cell_phone_number') {
            $changes .= (' [' . $key . '] ' . $old->cell_phone_number->name . ' => ' . $changed->cell_phone_number->name . ',');
          } elseif ($key == 'affiliate_state_id') {
            $changes .= (' [' . $key . '] ' . $old->affiliate_state_id->name . ' => ' . $changed->affiliate_state_id->name . ',');
          } elseif ($key == 'category_id') {
            $changes .= (' [' . $key . '] ' . $old->category_id->name . ' => ' . $changed->category_id->name . ',');
          } elseif ($key == 'date_entry') {
            $changes .= (' [' . $key . '] ' . $old->date_entry->name . ' => ' . $changed->date_entry->name . ',');
          } elseif ($key == 'degree_id') {
            $changes .= (' [' . $key . '] ' . $old->degree_id->name . ' => ' . $changed->degree_id->name . ',');
          } else {
            $changes .= (' [' . $key . '] ' . $old[$key] . ' => ' . $value . ',');
          }
        }
        Util::save_action($changes);
      }
    }
   /**
   * Handle the affiliate "deleted" event.
   *
   * @param  \App\Affiliate  $Affiliate
   * @return void
   */
    public function deleted(Affiliate $affiliate)
    {
        Util::save_action('Afiliado Eliminado: ' . $affiliate->fullName());
    }
}
