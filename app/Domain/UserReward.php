<?php

namespace App\Domain;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class UserReward
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
    /**
     * @ORM\ManyToOne(targetEntity=Reward::class)
     * @ORM\JoinColumn(name="reward_id", referencedColumnName="id")
     */
    private Reward $reward;
    /** @ORM\Column(type="integer") */
    private int $rewardId;
     /** @ORM\Column(type="integer") */
     private int $credits;
    /** @ORM\Column(type="datetime") */
    private ?\DateTime $claimedDate;
    
    public function __construct(User $user, Reward $reward, int $credits)
    {
        $this->user = $user;
        $this->reward = $reward;
        $this->rewardId = $rewardId;
        $this->claimedDate = Carbon::now();
        $this->userId = $user->getId();
        $this->credits = $credits;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getReward(): Reward
    {
        return $this->reward;
    }

    public function getRewardId(): int
    {
        return $this->rewardId;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function getClaimedDate()
    {
        return $this->claimedDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
