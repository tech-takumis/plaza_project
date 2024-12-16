<?php

namespace App\Events;

use App\Models\CertificateRequest;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApprovedCertificateEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected CertificateRequest $certificateRequest)
    {
        //
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->certificateRequest->user_id,
            'name' => $this->certificateRequest->user->name,
            'certificate_request_id' => $this->certificateRequest->id,
            'certificate_name' => $this->certificateRequest->certificate->name
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
            new PrivateChannel('user.certificate.approved.'.$this->certificateRequest->user_id),
        ];
    }
}
