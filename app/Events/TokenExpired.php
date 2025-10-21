<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TokenExpired implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $correo;

    public function __construct($correo)
    {
        $this->correo = $correo;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('token.' . $this->correo);
    }
}

