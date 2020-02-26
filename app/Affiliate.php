<?php

namespace App;
use Carbon;

use Illuminate\Database\Eloquent\Model;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Support\Facades\Storage;
use Util;

class Affiliate extends Model
{
    use Traits\EloquentGetTableNameTrait;
    use Traits\RelationshipsTrait;

    public $relationships = ['City', 'AffiliateState'];
    // protected $appends = ['picture_saved', 'fingerprint_saved', 'full_name'];
    // protected $hidden = ['pivot'];
    protected $fillable = [
        'user_id',
        'affiliate_state_id',
        'city_identity_card_id',
        'city_birth_id',
        'degree_id',
        'unit_id',
        'category_id',
        'pension_entity_id',
        'identity_card',
        'registration',
        'type',
        'last_name',
        'mothers_last_name',
        'first_name',
        'second_name',
        'surname_husband',
        'civil_status',
        'gender',
        'birth_date',
        'date_entry',
        'date_death',
        'reason_death',
        'date_derelict',
        'reason_derelict',
        'change_date',
        'phone_number',
        'cell_phone_number',
        'afp',
        'nua',
        'item',
        'is_duedate_undefined',
        'due_date'
      ];

    public function getTitleAttribute()
    {
        return $this->degree->shortened;
    }

    public function getPictureSavedAttribute()
    {
        $base_path = 'picture/';
        return Storage::disk('ftp')->exists($base_path . $this->id . '_perfil.jpg');
    }

    public function getIdentityCardExtAttribute()
    {
        $data = $this->identity_card;
        if ($this->city_identity_card) $data .= ' ' . $this->city_identity_card->first_shortened;
        return $data;
    }

    public function getCivilStatusAttribute($value)
    {
        return Util::get_civil_status($value, $this->gender);
    }

    public function getFingerprintSavedAttribute()
    {
        $base_path = 'picture/';
        $fingerprint_pictures = ['_left_four.png', '_right_four.png', '_thumbs.png'];
        $fingerprint_exists = false;
        foreach ($fingerprint_pictures as $picture) {
            $fingerprint_exists |= Storage::disk('ftp')->exists($base_path . $this->id . $picture);
        }
        return boolval($fingerprint_exists);
    }
    public function getFullNameAttribute()
    {
      return preg_replace('/[[:blank:]]+/', ' ', join(' ', [$this->first_name, $this->second_name, $this->last_name, $this->mothers_last_name]));
    }

    public function getDeadAttribute()
    {
        return ($this->date_death != null || $this->reason_death != null || $this->death_certificate_number != null);
    }

    public function getDefaultedAttribute()
    {
        $loans = $this->loans()->whereHas('state', function($q) {
            $q->where('name', 'Desembolsado ');
        })->get()->merge($this->guarantees()->whereHas('state', function($q) {
            $q->where('name', 'Desembolsado ');
        })->get());
        foreach ($loans as $loan) {
            if ($loan->defaulted) return true;
        }
        return false;
    }

    public function category()
    {
      return $this->belongsTo(Category::class);
    }
    public function degree()
    {
      return $this->belongsTo(Degree::class);
    }
    public function unit()
    {
      return $this->belongsTo(Unit::class);
    }
    public function city_identity_card()
    {
      return $this->belongsTo(City::class,'city_identity_card_id', 'id');
    }
    public function affiliate_state()
    {
      return $this->belongsTo(AffiliateState::class);
    }
    public function city_birth()
    {
      return $this->belongsTo(City::class, 'city_birth_id', 'id');
    }
    public function pension_entity()
    {
      return $this->belongsTo(PensionEntity::class);
    }
      // add records
    public function records()
    {
      return $this->morphMany(Record::class, 'recordable');
    }
      //address
    public function addresses()
    {
      return $this->morphToMany(Address::class, 'addressable')->withTimestamps();
    }

    public function getAddressAttribute()
    {
        return $this->addresses()->latest()->first();
    }

    //spouses
    public function spouses()
    {
      return $this->hasMany(Spouse::class);
    }
    public function getSpouseAttribute()
    {
      return $this->spouses()->first();
    }
    //contributions
    public function contributions()
    {
      return $this->hasMany(Contribution::class);
    }

    public function guarantees()
    {
        return $this->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['payment_percentage'])->whereGuarantor(true)->orderBy('loans.created_at', 'desc');
    }

    public function loans()
    {
        return $this->belongsToMany(Loan::class, 'loan_affiliates')->withPivot(['payment_percentage'])->whereGuarantor(false)->orderBy('loans.created_at', 'desc');
    }

    public function active_loans()
    {
        return $this->verify_balance($this->loans);
    }
    public function active_guarantees()
    {
        return $this->verify_balance($this->guarantees);
    }

    private function verify_balance($loans)
    {
        $active_loans = [];
        foreach ($loans as $loan) {
            $loan->balance = $loan->balance;
            if ($loan->balance > 0) {
                $loan->estimated_quota = $loan->estimated_quota;
                array_push($active_loans, $loan);
            }
        }
        return $active_loans;
    }

    //document
    public function submitted_documents()
    {
        return $this->hasMany(AffiliateSubmittedDocument::class);
    }

    public function disbursements()
    {
        return $this->morphMany(Loan::class, 'disbursable');
    }

    public function getCpopAttribute(){
      return false;  
    }
}
