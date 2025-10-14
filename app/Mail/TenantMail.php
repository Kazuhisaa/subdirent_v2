<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantMail extends Mailable
{
    public $email;
    public $password;

    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Your Tenant Credentials')
                    ->html("
                        <p>Hello Tenant,</p>
                        <p>Here are your credentials:</p>
                        <p>Email: {$this->email}</p>
                        <p>Password: {$this->password}</p>
                    ");
    }
}


