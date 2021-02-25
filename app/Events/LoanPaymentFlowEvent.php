<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoanPaymentFlowEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($loanPayments)
    {
        $this->loanPayments = $loanPayments;
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('loan_payments');
    }

    public function broadcastAs()
    {
        return 'flow';
    }

    public function broadcastWith()
    {
        return [
            'data' => [
                'derived' => $this->loanPayments,
                'role_id' => $this->loanPayments[0]->role_id
            ]
        ];
    }
}
