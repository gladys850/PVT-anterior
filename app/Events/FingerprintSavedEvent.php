<?php

namespace App\Events;

use App\Affiliate;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FingerprintSavedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $affiliate, $user, $status;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Affiliate $affiliate, User $user, $status)
    {
        $this->affiliate = $affiliate;
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('fingerprint');
    }

    public function broadcastAs()
    {
        return 'saved';
    }

    public function broadcastWith()
    {
        return [
            'data' => [
                'affiliate_id' => $this->affiliate->id,
                'user_id' => $this->user->id,
                'success' => $this->status
            ]
        ];
    }
}
