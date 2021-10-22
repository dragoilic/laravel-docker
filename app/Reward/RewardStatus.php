<?php
namespace App\Reward;

use MyCLabs\Enum\Enum;

/**
 * @method static RewardStatus ACTIVE()
 * @method static RewardStatus INACTIVE()
 */
final class RewardStatus extends Enum
{
    private const ACTIVE = "Active";
    private const INACTIVE = "Inactive";
}
