<?php

namespace App\Console\Commands;
use App\LoanPayment;
use App\LoanState;
use Illuminate\Console\Command;

class LoanPaymentChangeState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loanPayment:changeState';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registros de pagos no cancelados a anulados';

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
        $PendientePago = LoanState::whereName('Pendiente de Pago')->first()->id;
        $Anulado = LoanState::whereName('Anulado')->first()->id;
        $loanPayment = LoanPayment::where('state_id', $PendientePago);
        $loanPayment->update(['state_id' => $Anulado]);
    }
}
