<?php

namespace App\Console\Commands;
use App\Loan;
use App\Spouse;
use Util;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


use Illuminate\Console\Command;

class LoanUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loan:loan_update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'copia datos del diksbursable id al afiliado';

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
    {
        $loan = Loan::withTrashed()->get();
        foreach($loan as $loan_update){
        $loan_update = Loan::withTrashed()->where('id',$loan_update->id)->first();
            if($loan_update->disbursable_type == "affiliates"){
                 $loan_update->affiliate_id = $loan_update->disbursable_id;
            }else{
                if($loan_update->disbursable_type == "spouses"){
                 $loan_update->affiliate_id = Spouse::find($loan_update->disbursable_id)->affiliate_id;
             }
            }     
         $loan_update->save();
        }
     Schema::table('loans', function (Blueprint $table) {
         $table->dropColumn(['disbursable_type']);
         $table->dropColumn(['disbursable_id']);
     });
    }
}
