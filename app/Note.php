<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public function annontable()
    {
        return $this->morphTo();
    }
}
