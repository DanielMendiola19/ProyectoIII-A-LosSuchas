<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $correo;

    public function __construct($correo, $token)
    {
        $this->correo = $correo;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Código de verificación Coffeeology')
                    ->markdown('emails.send-token')
                    ->with([
                        'token' => $this->token,
                        'correo' => $this->correo,
                    ]);
    }
}