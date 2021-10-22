<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Referral extends Mailable
{
    use Queueable, SerializesModels;

    private string $email;
    private string $referralLink;

    public function __construct(string $email, string $referralLink)
    {
        $this->email = $email;
        $this->referralLink = $referralLink;
    }

    public function build()
    {
        return $this->view('emails.referral')
            ->with('email', $this->email)
            ->with('referralLink', $this->referralLink);
    }
}
