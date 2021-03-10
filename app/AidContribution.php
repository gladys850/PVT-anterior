<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AidContribution extends Model
{
    use Traits\EloquentGetTableNameTrait;
    protected $fillable = [

        'user_id',
        'affiliate_id',
        'month_year',
        'type',
        'quotable',
        'rent',
        'dignity_rent',
        'interest',
        'total',
        'affiliate_contribution',
        'mortuary_aid',
        'valid'

    ];
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }
      //relacion de la tabla polimorfica ajuste de contribuciones
    public function loan_contribution_adjusts()
    {
        return $this->morphMany(LoanContrubutionAdjust::class,'adjustable');
    }
}
