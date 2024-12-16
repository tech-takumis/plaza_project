<?php

namespace App\Events;

use App\Models\Certificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCertificateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected Certificate $certificate)
    {
        //
    }

    public function broadcastWith()
    {

        $this->certificate->load(['attributes', 'requirements']);
        return [
            'id'=> $this->certificate->id,
            'name' => $this->certificate->name,
            'description' => $this->certificate->description,
            'template' => asset($this->certificate->template),
            'validity' => $this->certificate->validity,
            'requirements' => $this->certificate->requirements->toArray(),
            'attributes' => $this->certificate->attributes->toArray(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('staff.notifications'),
            new PrivateChannel('user.notifications'),
        ];
    }
}
