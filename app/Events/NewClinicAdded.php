<?php

namespace App\Events;

use App\Models\Clinic;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewClinicAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Clinic $clinic)
    {
        $this->clinic->loadMissing('user');
    }

    public function broadcastOn(): array
    {
        $channels = [new PresenceChannel('clinics')];

        if ($this->clinic->user) {
            $channels[] = new Channel('user.'.$this->clinic->user->id);
        }

        return $channels;
    }
}
