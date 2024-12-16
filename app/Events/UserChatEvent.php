<?php

namespace App\Events;

use App\Models\Staff;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected User $sender, protected UserMessage $message,protected Staff $receiver)
    {
        //
    }

    public function broadcastWith()
    {
        return [
            'sender_id' => $this->sender->id,
            'receiver_id' => $this->receiver->id,
            'sender_name' => $this->sender->name,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at,
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
            new PrivateChannel('chat.'.$this->receiver->id),
        ];
    }
}