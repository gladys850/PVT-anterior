<?php

namespace App\Imports;

use App\LoanPayment;
use Maatwebsite\Excel\Concerns\ToModel;

class LoanPaymentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       
    }
}
