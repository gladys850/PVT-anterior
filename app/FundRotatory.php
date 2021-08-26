<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FundRotatory extends Model
{
    protected $table = 'fund_rotatories';

    public $timestamps = true;
    public $guarded = ['id'];
    public $fillable = ['code_entry','check_number','amount','balance', 'balance_previous','date_check_delivery','description','user_id','role_id'];
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getLastAttribute()
    {
        return $this->get()->last();
    }
    public function fund_rotatory_outputs()
    {
        return $this->hasMany(FundRotatoryOutput::class);
    }
    // add records
    public function records()
    {
      return $this->morphMany(Record::class, 'recordable');
    }
    //verificar desembolsos del fondo rotatorio
    public function verify_fund_rotatory_disbursements()
    {
        $has_disbursements = true;
        $query = "SELECT count(fro.id) AS cant_found_rotatory_outputs
                  FROM fund_rotatories fr
                  JOIN fund_rotatory_outputs fro ON fr.id = fro.fund_rotatory_id
                  WHERE fro.deleted_at is null AND fr.id = $this->id";

        $cant_found_rotatory_outputs = DB::select($query);

        if($cant_found_rotatory_outputs[0]->cant_found_rotatory_outputs == 0) $has_disbursements = false;
        return $has_disbursements;
    }
//egreso
    public function getEgressAttribute()
    {
        $egress_fund_rotatory =DB::select('select egress_fund_rotatory('.$this->id.')');
        return $egress_fund_rotatory[0]->egress_fund_rotatory;
    }
}
