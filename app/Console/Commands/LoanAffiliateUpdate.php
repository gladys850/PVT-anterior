<?php

namespace App\Console\Commands;
use App\Loan;
use App\Spouse;
use App\Affiliate;
use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class LoanAffiliateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loanAffiliate:loan_affiliate_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion del campo type en LoanAffiliate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   //Spouses
        $loan_spouses="Select s.affiliate_id from loan_affiliates as la, spouses as s where  s.affiliate_id=la.affiliate_id";

        $update_spouses = "update loan_affiliates set type = 'spouses' where affiliate_id in($loan_spouses)";
        $update_spouses = DB::select($update_spouses);
        //affiliate
        $loan_affiliate="Select la.affiliate_id from loan_affiliates as la where la.type is null";

        $update_affiliate = "update loan_affiliates set type = 'affiliates' where affiliate_id in($loan_affiliate)";
        $update_affiliate = DB::select($update_affiliate);
    
    }
}
