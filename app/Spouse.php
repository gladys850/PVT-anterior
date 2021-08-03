<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laratrust\Traits\LaratrustUserTrait;
use Util;

class Spouse extends Model
{
    use Traits\EloquentGetTableNameTrait;
    protected $fillable = [
        'affiliate_id',
        'city_identity_card_id',
        'identity_card',
        'registration',
        'last_name',
        'mothers_last_name',
        'first_name',
        'second_name',
        'surname_husband',
        'civil_status',
        'birth_date',
        'date_death',
        'reason_death',
        'city_birth_id',
        'death_certificate_number',
        'due_date',
        'is_duedate_undefined',
        'official',
        'book',
        'departure',
        'marriage_date',
    ];

    public function getCivilStatusGenderAttribute()
    {
        $civil_status = Util::get_civil_status($this->civil_status, $this->gender);
        unset($this->affiliate);
        return $civil_status;
    }

    public function getTitleAttribute()
    {   return '';
        //return 'Vd' . ($this->affiliate->gender == 'M' ? 'a' : 'o') . '.';
    }

    public function getGenderAttribute()
    {
        return $this->affiliate->gender == 'M' ? 'F' : 'M';
    }

    public function getIdentityCardExtAttribute()
    {
        return $this->identity_card . ' ' . $this->city_identity_card->first_shortened;
    }

    public function getFullNameAttribute()
    {
        return preg_replace('/[[:blank:]]+/', ' ', join(' ', [$this->first_name, $this->second_name, $this->last_name, $this->mothers_last_name,$this->surname_husband]));
    }

    public function getAddressAttribute()
    {
        return $this->affiliate->address;
    }

    public function getCellPhoneNumberAttribute()
    {
        return $this->affiliate->cell_phone_number;
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
    public function city_identity_card()
    {
        return $this->belongsTo(City::class, 'city_identity_card_id', 'id');
    }
    public function city_birth()
    {
        return $this->belongsTo(City::class, 'city_birth_id', 'id');
    }
    
    public function active_loans()
    {
        return $this->affiliate->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['type'])->where('type', 'spouses')->whereGuarantor(false)->whereIn('state_id', [1,3])->count();
    }
    public function loans(){
        return $this->affiliate->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['type'])->where('type', 'spouses')->whereGuarantor(false)->whereIn('state_id', [1,3])->orderBy('loans.created_at', 'desc');
    }
    public function spouse_loans(){
        return $this->affiliate->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['type'])->where('type', 'spouses')->whereGuarantor(false)->orderBy('loans.created_at', 'desc');
    }
    public function current_loans()
    {
      $loan_state = LoanState::whereName('Vigente')->first();
      return $this->affiliate->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['payment_percentage'])->whereGuarantor(false)->where('state_id', $loan_state->id)->orderBy('loans.created_at', 'desc');
    }
    public function guarantees(){
        return $this->affiliate();
    }

    public function active_guarantees_sismu(){
        $query = "SELECT Prestamos.IdPrestamo, Prestamos.PresNumero, Prestamos.IdPadron, Prestamos.PresCuotaMensual, Prestamos.PresEstPtmo, Prestamos.PresMeses
        FROM Padron
        join PrestamosLevel1 on PrestamosLevel1.IdPadronGar = Padron.IdPadron
        join Prestamos on PrestamosLevel1.IdPrestamo = prestamos.IdPrestamo
        where Padron.PadCedulaIdentidad = '$this->identity_card'
        and Prestamos.PresEstPtmo = 'V'";
        $loans = DB::connection('sqlsrv')->select($query);
        foreach($loans as $loan){
          $query = "SELECT count(*) as quantity
            from PrestamosLevel1
            where PrestamosLevel1.IdPrestamo = '$loan->IdPrestamo'";
            $quantity = DB::connection('sqlsrv')->select($query);
            $loan->quantity_guarantors = $quantity[0]->quantity;
        }
        return $loans;
       }
    
       public function active_loans_sismu(){
        $query = "SELECT Prestamos.IdPrestamo, Prestamos.PresNumero, Prestamos.IdPadron, Prestamos.PresCuotaMensual, Prestamos.PresEstPtmo, Prestamos.PresMeses
        FROM Prestamos
        join Padron on Prestamos.IdPadron = Padron.IdPadron
        where Padron.PadCedulaIdentidad = '$this->identity_card'
        and Prestamos.PresEstPtmo = 'V'";
        $loans = DB::connection('sqlsrv')->select($query);
        return $loans;
       }
    
       public function process_loans_sismu(){
        $query = "SELECT Prestamos.IdPrestamo, Prestamos.PresNumero, Prestamos.IdPadron, Prestamos.PresCuotaMensual, Prestamos.PresEstPtmo, Prestamos.PresMeses
        FROM Prestamos
        join Padron on Prestamos.IdPadron = Padron.IdPadron
        where Padron.PadCedulaIdentidad = '$this->identity_card'
        and Prestamos.PresEstPtmo = 'A'";
        $loans = DB::connection('sqlsrv')->select($query);
        return $loans;
       }
    
       public function process_guarantees_sismu(){
        $query = "SELECT Prestamos.IdPrestamo, Prestamos.PresNumero, Prestamos.IdPadron, Prestamos.PresCuotaMensual, Prestamos.PresEstPtmo, Prestamos.PresMeses
        FROM Padron
        join PrestamosLevel1 on PrestamosLevel1.IdPadronGar = Padron.IdPadron
        join Prestamos on PrestamosLevel1.IdPrestamo = prestamos.IdPrestamo
        where Padron.PadCedulaIdentidad = '$this->identity_card'
        and Prestamos.PresEstPtmo = 'A'";
        $loans = DB::connection('sqlsrv')->select($query);
        foreach($loans as $loan){
          $query = "SELECT count(*) as quantity
            from PrestamosLevel1
            where PrestamosLevel1.IdPrestamo = '$loan->IdPrestamo'";
            $quantity = DB::connection('sqlsrv')->select($query);
            $loan->quantity = $quantity->quantity;
        }
        return $loans;
       }

       public function getInitialsAttribute(){
        return (substr($this->first_name, 0, 1).substr($this->second_name, 0, 1).substr($this->last_name, 0, 1).substr($this->mothers_last_name, 0, 1).substr($this->surname_husband, 0, 1));
      }
}
