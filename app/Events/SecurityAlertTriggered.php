<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SecurityAlertTriggered
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $userId;
    public $email;
    public $ip;
    public $reason;
    public $level;

    public function __construct($userId, $email, $ip, $reason, $level = 'error')
    {
        $this->userId = $userId; // Puede ser null si el correo no existe en BD
        $this->email = $email;
        $this->ip = $ip;
        $this->reason = $reason;
        $this->level = $level;
    }
}