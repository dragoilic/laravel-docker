<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserReward extends Mailable
{
    use Queueable, SerializesModels;

    private string $name;
    private string $fullname;
    private string $prizeCost;
    private array $address;

    public function __construct(string $name, string $fullname, string $prizeCost, array $address)
    {
        $this->name = $name;
        $this->fullname = $fullname;
        $this->prizeCost = $prizeCost;
        $this->address = $address;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.user-redeem-reward')
            ->with('name', $this->name)
            ->with('fullname', $this->fullname)
            ->with('prizeCost', $this->prizeCost)
            ->with('address', $this->address);
    }
}
