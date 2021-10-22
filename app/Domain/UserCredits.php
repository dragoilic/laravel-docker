<?php

namespace App\Domain;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class UserCredits
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;
    
    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private User $user;
    /** @ORM\Column(type="integer") */
    private int $userId;
    /** @ORM\Column(type="integer") */
    private int $credits;
    /** @ORM\Column(type="string") */
    private string $reason;
    /** @ORM\Column(type="datetime") */
    private \DateTime $paidDate;
    
    public function __construct(User $user, int $credits, string $reason)
    {
        $this->user = $user;
        $this->userId = $user->getId();
        $this->credits = $credits;
        $this->reason = $reason;
        $this->paidDate = Carbon::now();
    }

    public function getId(): int
    {
        return $this->id;
    }

        public function getUser(): User
    {
        return $this->user;
    }

    public function getRewardId(): int
    {
        return $this->rewardId;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getPaidDate()
    {
        return $this->paidDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
